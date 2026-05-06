<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Entrepreneur;
use Illuminate\Http\Request;

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

        $stats = [
            'total' => Entrepreneur::count(),
            'pending' => Entrepreneur::where('status', Entrepreneur::STATUS_PENDING)->count(),
            'approved' => Entrepreneur::where('status', Entrepreneur::STATUS_APPROVED)->count(),
            'rejected' => Entrepreneur::where('status', Entrepreneur::STATUS_REJECTED)->count(),
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
        $entrepreneurs = Entrepreneur::with('user')
            ->when($request->status, fn($q, $s) => $q->where('status', $s))
            ->when($request->search, fn($q, $s) => $q->where('name', 'like', "%{$s}%"))
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return inertia('Admin/Entrepreneurs', [
            'entrepreneurs' => $entrepreneurs,
            'filters' => $request->only(['status', 'search']),
        ]);
    }

    /**
     * 审批通过
     */
    public function approve(Entrepreneur $entrepreneur)
    {
        $entrepreneur->update(['status' => Entrepreneur::STATUS_APPROVED]);

        return redirect()->back()->with('success', "已通过 {$entrepreneur->name} 的认证申请");
    }

    /**
     * 审批拒绝
     */
    public function reject(Entrepreneur $entrepreneur)
    {
        $entrepreneur->update(['status' => Entrepreneur::STATUS_REJECTED]);

        return redirect()->back()->with('success', "已拒绝 {$entrepreneur->name} 的认证申请");
    }

    /**
     * 设为推荐 / 取消推荐
     */
    public function toggleFeatured(Entrepreneur $entrepreneur)
    {
        $entrepreneur->update(['is_featured' => !$entrepreneur->is_featured]);

        $status = $entrepreneur->is_featured ? '设为推荐' : '取消推荐';
        return redirect()->back()->with('success', "已将 {$entrepreneur->name} {$status}");
    }

    /**
     * 删除企业家档案
     */
    public function destroy(Entrepreneur $entrepreneur)
    {
        $name = $entrepreneur->name;
        $entrepreneur->delete();

        return redirect()->route('admin.entrepreneurs')->with('success', "已删除 {$name}");
    }
}
