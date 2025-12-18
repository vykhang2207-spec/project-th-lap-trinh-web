<?php

namespace App\Policies;

use App\Models\Course;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class CoursePolicy
{
    // Admin co quyen thuc hien moi hanh dong
    public function before(User $user, string $ability): bool|null
    {
        if ($user->role === 'admin') {
            return true;
        }
        return null;
    }

    // Chi giao vien moi duoc tao khoa hoc
    public function create(User $user): bool
    {
        return $user->role === 'teacher';
    }

    // Chi giao vien so huu khoa hoc moi duoc sua
    public function update(User $user, Course $course): bool
    {
        return $user->role === 'teacher' && $user->id === $course->teacher_id;
    }

    // Chi giao vien so huu khoa hoc moi duoc xoa
    public function delete(User $user, Course $course): bool
    {
        return $user->role === 'teacher' && $user->id === $course->teacher_id;
    }
}
