<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; color: #222; }
        h1, h2 { margin: 0 0 10px; }
        .meta { margin-bottom: 20px; }
        .meta span { display: inline-block; margin-right: 20px; font-size: 11px; color: #555; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { border: 1px solid #ccc; padding: 8px; text-align: left; }
        th { background: #f5f5f5; }
        tbody tr:nth-child(even) { background: #fafafa; }
    </style>
</head>
<body>
    <h1>Patient Report</h1>
    <div class="meta">
        <?php if($fromDate): ?>
            <span>From: <?php echo e($fromDate); ?></span>
        <?php endif; ?>
        <?php if($toDate): ?>
            <span>To: <?php echo e($toDate); ?></span>
        <?php endif; ?>
        <?php if($wardName): ?>
            <span>Ward: <?php echo e($wardName); ?></span>
        <?php endif; ?>
        <?php if($specialtyName): ?>
            <span>Specialty: <?php echo e($specialtyName); ?></span>
        <?php endif; ?>
        <?php if($status): ?>
            <span>Status: <?php echo e(ucfirst($status)); ?></span>
        <?php endif; ?>
    </div>

    <table>
        <thead>
            <tr>
                <th>GHIMS</th>
                <th>Patient Name</th>
                <th>Ward</th>
                <th>Specialty</th>
                <th>Status</th>
                <th>Time In</th>
                <th>Created At</th>
            </tr>
        </thead>
        <tbody>
            <?php $__currentLoopData = $patients; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $patient): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <tr>
                    <td><?php echo e($patient->ghims_number); ?></td>
                    <td><?php echo e($patient->patient_name); ?></td>
                    <td><?php echo e($patient->ward?->name ?? 'Unassigned'); ?></td>
                    <td><?php echo e($patient->team?->name ?? 'Unassigned'); ?></td>
                    <td><?php echo e(ucfirst($patient->status)); ?></td>
                    <td><?php echo e(optional($patient->time_in)->format('Y-m-d H:i:s') ?? 'N/A'); ?></td>
                    <td><?php echo e($patient->created_at->format('Y-m-d H:i:s')); ?></td>
                </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </tbody>
    </table>
</body>
</html>
<?php /**PATH /Applications/XAMPP/xamppfiles/htdocs/White Board/resources/views/reports/export-pdf.blade.php ENDPATH**/ ?>