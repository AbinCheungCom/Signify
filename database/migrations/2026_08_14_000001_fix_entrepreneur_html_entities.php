<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * 数据修复：解码 entrepreneurs 表文本字段里被实体化的字符。
     *
     * 背景：历史数据中英文双引号 " 被存成了 &quot; 实体（中文引号不受影响）。
     * 页面用 {{ }} 展示时 & 再被转义一次，于是用户看到字面的 &quot;。
     *
     * 这里把 &quot; / &#34; / &lt; / &gt; / &amp; / &#39; 等解码回原字符，
     * 反复解码直至稳定，兼容一次/多次编码。仅更新实际变化的行，可重复执行。
     */
    public function up(): void
    {
        // strtr 数组按最长匹配优先，二次编码（&amp;quot;）会先于 &amp; 被替换
        $map = [
            '&amp;quot;' => '"',
            '&amp;lt;'   => '<',
            '&amp;gt;'   => '>',
            '&amp;amp;'  => '&',
            '&amp;#039;' => "'",
            '&amp;#39;'  => "'",
            '&quot;'     => '"',
            '&#34;'      => '"',
            '&#x22;'     => '"',
            '&#039;'     => "'",
            '&#39;'      => "'",
            '&lt;'       => '<',
            '&gt;'       => '>',
            '&amp;'      => '&',
        ];

        // 用户可编辑的文本字段
        $columns = ['name', 'title', 'industry', 'city', 'bio', 'contact_phone', 'contact_email'];

        foreach ($columns as $column) {
            $rows = DB::table('entrepreneurs')
                ->whereNotNull($column)
                ->where($column, 'like', '%&%')
                ->pluck($column, 'id');

            foreach ($rows as $id => $value) {
                $cleaned = $value;
                do {
                    $prev = $cleaned;
                    $cleaned = strtr($cleaned, $map);
                } while ($cleaned !== $prev);

                if ($cleaned !== $value) {
                    DB::table('entrepreneurs')->where('id', $id)->update([$column => $cleaned]);
                }
            }
        }
    }

    /**
     * 数据修复不可逆，down 留空。
     */
    public function down(): void
    {
        // 无操作
    }
};
