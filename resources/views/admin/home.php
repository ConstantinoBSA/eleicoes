<?php startSection('title'); ?>
Teste
<?php endSection(); ?>

<?php startSection('content'); ?>
<div class="row">
    <div class="col-md-6 offset-md-6">
        <nav aria-label="breadcrumb" class="d-flex justify-content-end">
            <ol class="breadcrumb">
                <li class="breadcrumb-item active" aria-current="page"><i class="fa fa-dashboard fa-fw"></i> Dashboard</li>
            </ol>
        </nav>
    </div>
</div>


<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
<?php endSection(); ?>

<?php extend('layouts/admin'); ?>
