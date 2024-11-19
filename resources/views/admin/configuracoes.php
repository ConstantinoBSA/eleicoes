<?php startSection('title'); ?>
Teste
<?php endSection(); ?>

<?php startSection('content'); ?>
<div class="row mb-2">
    <div class="col-md-6">
        <h4 class="titulo-pagina">
            <span><i class="fa fa-cogs fa-fw"></i> Configurações</span>
            <small>Gerenciamento das configurações</small>
        </h4>
    </div>
    <div class="col-md-6">
        <nav aria-label="breadcrumb" class="d-flex justify-content-end">
            <ol class="breadcrumb">
				<li class="breadcrumb-item"><a href="/admin/dashboard"><i class="fa fa-dashboard fa-fw"></i> Dashboard</a></li>
				<li class="breadcrumb-item active" aria-current="page">Configurações</li>
            </ol>
        </nav>
    </div>
</div>

<div class="row mt-5">
	<form method="post" action="/admin/configuracoes/salvar">
		<div class="row mb-3">
			<label for="config_site_name" class="col-sm-3 col-form-label text-end text-muted">Site Name:</label>
			<div class="col-sm-6">
				<input type="text" class="form-control" id="config_site_name" 
					name="config[site_name]" 
					value="<?php echo htmlspecialchars(config()['site_name'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
				<div id="emailHelp" class="form-text">We'll never share your email with anyone else.</div>
			</div>
		</div>

		<div class="row mb-3">
			<label for="config_maintenance_mode" class="col-sm-3 col-form-label text-end text-muted">Maintenance Mode:</label>
			<div class="col-sm-6">
				<input type="text" class="form-control" id="config_maintenance_mode" 
					name="config[maintenance_mode]" 
					value="<?php echo htmlspecialchars(config()['maintenance_mode'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
			</div>
		</div>

		<div class="row mb-3">
			<label for="config_items_per_page" class="col-sm-3 col-form-label text-end text-muted">Items Per Page:</label>
			<div class="col-sm-6">
				<input type="text" class="form-control" id="config_items_per_page" 
					name="config[items_per_page]" 
					value="<?php echo htmlspecialchars(config()['items_per_page'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
			</div>
		</div>

		<div class="row mb-3">
			<label for="config_contact_email" class="col-sm-3 col-form-label text-end text-muted">Contact Email:</label>
			<div class="col-sm-6">
				<input type="email" class="form-control" id="config_contact_email" 
					name="config[contact_email]" 
					value="<?php echo htmlspecialchars(config()['contact_email'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
			</div>
		</div>

		<div class="row mb-3">
			<label for="config_timezone" class="col-sm-3 col-form-label text-end text-muted">Timezone:</label>
			<div class="col-sm-6">
				<input type="text" class="form-control" id="config_timezone" 
					name="config[timezone]" 
					value="<?php echo htmlspecialchars(config()['timezone'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
			</div>
		</div>

		<div class="row mb-3">
			<label for="config_enable_signups" class="col-sm-3 col-form-label text-end text-muted">Enable Signups:</label>
			<div class="col-sm-6">
				<input type="text" class="form-control" id="config_enable_signups" 
					name="config[enable_signups]" 
					value="<?php echo htmlspecialchars(config()['enable_signups'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
			</div>
		</div>

		<div class="row mb-5">
			<label for="config_currency" class="col-sm-3 col-form-label text-end text-muted">Currency:</label>
			<div class="col-sm-6">
				<input type="text" class="form-control" id="config_currency" 
					name="config[currency]" 
					value="<?php echo htmlspecialchars(config()['currency'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
			</div>
		</div>

		<div class="col-sm-6 offset-3 text-center mb-4">
			<button type="submit" class="btn btn-primary"><i class="fa fa-check fa-fw"></i> Salvar Configurações</button>
		</div>
	</form>
</div>
<?php endSection(); ?>

<?php extend('layouts/admin'); ?>
