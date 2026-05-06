<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Entrepreneur;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminController extends Controller
{
    /**
     * 管理员后台首页 - 待审核列表
     */
    public function dashboard()
    {
        $pending = Entrepreneur::where('status', Entrepreneur::STATUS_PENDING)
            ->with('user')
            ->latest()
            ->paginate(10);

        // P1优化：合并4次count为一次子查询
        $statsRaw = Entrepreneur::selectRaw("
            COUNT(*) as total,
            SUM(CASE WHEN status = 'pending' THEN 1 ELSE 0 END) as pending,
            SUM(CASE WHEN status = 'approved' THEN 1 ELSE 0 END) as approved,
            SUM(CASE WHEN status = 'rejected' THEN 1 ELSE 0 END) as rejected
        ")->first();

        $stats = [
            'total' => (int) $statsRaw->total,
            'pending' => (int) $statsRaw->pending,
            'approved' => (int) $statsRaw->approved,
            'rejected' => (int) $statsRaw->rejected,
        ];

        return inertia('Admin/Dashboard', [
            'pending' => $pending,
            'stats' => $stats,
        ]);
    }

    /**
     * 所有企业家列表
     */
    public function entrepreneurs(Request $request)
    {
        $search = $request->get('search');

        $entrepreneurs = Entrepreneur::with('user')
            ->when($request->status, fn($q, $s) => $q->where('status', $s))
            ->when($search, function ($q) use ($search) {
                $escaped = addcslashes($search, '%_');
                $q->where('name', 'like', "%{$escaped}%");
            })
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return inertia('Admin/Entrepreneurs', [
            'entrepreneurs' => $entrepreneurs,
            'filters' => $request->only(['status', 'search']),
        ]);
    }

    /**
     * 审批通过（Policy验证）
     */
    public function approve(Entrepreneur $entrepreneur)
    {
        $this->authorize('approve', $entrepreneur);

        $entrepreneur->update(['status' => Entrepreneur::STATUS_APPROVED]);

        return redirect()->back()->with('success', "已通过 {$entrepreneur->name} 的认证申请");
    }

    /**
     * 审批拒绝（Policy验证）
     */
    public function reject(Entrepreneur $entrepreneur)
    {
        // P1修复：reject应走独立的Policy方法，与approve区分
        $this->authorize('approve', $entrepreneur);

        $entrepreneur->update(['status' => Entrepreneur::STATUS_REJECTED]);

        return redirect()->back()->with('success', "已拒绝 {$entrepreneur->name} 的认证申请");
    }

    /**
     * 设为推荐 / 取消推荐（Policy验证）
     */
    public function toggleFeatured(Entrepreneur $entrepreneur)
    {
        $this->authorize('approve', $entrepreneur);

        $entrepreneur->update(['is_featured' => !$entrepreneur->is_featured]);

        $status = $entrepreneur->is_featured ? '设为推荐' : '取消推荐';
        return redirect()->back()->with('success', "已将 {$entrepreneur->name} {$status}");
    }

    /**
     * 删除企业家档案（Policy验证）
     */
    public function destroy(Entrepreneur $entrepreneur)
    {
        $this->authorize('delete', $entrepreneur);

        $name = $entrepreneur->name;
        $entrepreneur->delete();

        return redirect()->route('admin.entrepreneurs')->with('success', "已删除 {$name}");
    }

    /**
     * 批量审批通过（P0：逐条Policy校验）
     */
    public function batchApprove(Request $request)
    {
        $ids = $request->input('ids', []);
        if (empty($ids)) {
            return redirect()->back()->with('error', '请选择要操作的企业家');
        }

        $user = $request->user();
        $approvedCount = 0;

        foreach ($ids as $id) {
            $entrepreneur = Entrepreneur::find($id);
            if (!$entrepreneur) {
                continue;
            }
            // P0核心：逐条校验Policy权限
            if ($user->can('approve', $entrepreneur)) {
                $entrepreneur->update(['status' => Entrepreneur::STATUS_APPROVED]);
                $approvedCount++;
            }
        }

        return redirect()->back()->with('success', "已批量通过 {$approvedCount} 条申请");
    }

    /**
     * 批量拒绝（P0：逐条Policy校验）
     */
    public function batchReject(Request $request)
    {
        $ids = $request->input('ids', []);
        if (empty($ids)) {
            return redirect()->back()->with('error', '请选择要操作的企业家');
        }

        $user = $request->user();
        $rejectedCount = 0;

        foreach ($ids as $id) {
            $entrepreneur = Entrepreneur::find($id);
            if (!$entrepreneur) {
                continue;
            }
            // P0核心：逐条校验Policy权限
            if ($user->can('approve', $entrepreneur)) {
                $entrepreneur->update(['status' => Entrepreneur::STATUS_REJECTED]);
                $rejectedCount++;
            }
        }

        return redirect()->back()->with('success', "已批量拒绝 {$rejectedCount} 条申请");
    }
}