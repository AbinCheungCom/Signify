<?php

namespace Database\Factories;

use App\Models\Entrepreneur;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Entrepreneur>
 */
class EntrepreneurFactory extends Factory
{
    protected $model = Entrepreneur::class;

    /**
     * 定义模型的状态
     */
    public function definition(): array
    {
        $industries = ['科技', '金融', '制造', '医疗', '教育', '零售', '能源', '建筑', '物流', '文化'];
        $cities = ['北京', '上海', '深圳', '广州', '杭州', '成都', '武汉', '南京', '西安', '苏州'];

        return [
            'user_id' => User::factory(),
            'name' => fake()->name(),
            'avatar' => null,
            'industry' => fake()->randomElement($industries),
            'city' => fake()->randomElement($cities),
            'bio' => fake()->paragraph(2),
            'contact_phone' => fake()->phoneNumber(),
            'contact_email' => fake()->safeEmail(),
            'is_featured' => fake()->boolean(20), // 20% 概率为推荐
            'status' => Entrepreneur::STATUS_APPROVED,
        ];
    }

    /**
     * 待审核状态
     */
    public function pending(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => Entrepreneur::STATUS_PENDING,
        ]);
    }

    /**
     * 已拒绝状态
     */
    public function rejected(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => Entrepreneur::STATUS_REJECTED,
        ]);
    }

    /**
     * 推荐企业家
     */
    public function featured(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_featured' => true,
        ]);
    }
}
