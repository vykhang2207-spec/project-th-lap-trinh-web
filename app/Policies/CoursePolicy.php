<?php

namespace App\Policies;

use App\Models\Course;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class CoursePolicy
{
    // Bỏ qua check Policy nếu User là Admin
    public function before(User $user, string $ability): bool|null
    {
        if ($user->role === 'admin') {
            return true; // Admin có quyền làm MỌI THỨ
        }
        return null; // Tiếp tục kiểm tra các hàm bên dưới
    }

    /**
     * Cho phép User tạo một Khóa học mới.
     * (Chỉ Giảng viên mới được tạo khóa học)
     */
    public function create(User $user): bool
    {
        return $user->role === 'teacher';
    }

    /**
     * Cho phép User cập nhật (update) Khóa học.
     * (Chỉ Giảng viên sở hữu khóa học đó mới được sửa)
     */
    public function update(User $user, Course $course): bool
    {
        // User phải là Giảng viên VÀ ID của User phải khớp với teacher_id của khóa học
        return $user->role === 'teacher' && $user->id === $course->teacher_id;
    }

    /**
     * Cho phép User xóa Khóa học.
     * (Giống như update, chỉ Giảng viên sở hữu mới được xóa)
     */
    public function delete(User $user, Course $course): bool
    {
        return $user->role === 'teacher' && $user->id === $course->teacher_id;
    }
}
