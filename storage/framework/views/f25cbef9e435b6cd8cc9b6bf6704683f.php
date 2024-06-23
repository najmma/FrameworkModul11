<?php $__env->startSection('content'); ?>

<div class="container mt-4">
    <div class="row mb-0">
        <div class="col-lg-9 col-xl-10">
            <h4 class="mb-3"><?php echo e($pageTitle); ?></h4>
        </div>
        <div class="col-lg-3 col-xl-2">
            <div class="d-grid gap-2">
                <a href="<?php echo e(route('employees.create')); ?>" class="btn
btn-primary">Create Employee</a>
            </div>
        </div>
    </div>
    <hr>
    <div class="table-responsive border p-3 rounded-3">
        <table class="table table-bordered table-hover table-striped mb-0
        bg-white">
            <thead>
                <tr>
                    <th>First Name</th>
                    <th>Last Name</th>
                    <th>Email</th>
                    <th>Age</th>
                    <th>Position</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php $__currentLoopData = $employees; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $employee): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <tr>
                        <td><?php echo e($employee->firstname); ?></td>
                        <td><?php echo e($employee->lastname); ?></td>
                        <td><?php echo e($employee->email); ?></td>
                        <td><?php echo e($employee->age); ?></td>
                        <td><?php echo e($employee->position->name); ?></td>
                        <td><?php echo $__env->make('employee.actions', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?></td>
                    </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </tbody>
        </table>
    </div>
</div>

<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH E:\Anugrah Putra Syifa Al Ghifari\Documents\ITTS\Mapel\Semester 4\FRAMEWORK\Praktikum\Praktikum 8\Tugas\syifa4-app\resources\views/employee/index.blade.php ENDPATH**/ ?>