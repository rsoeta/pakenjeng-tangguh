<?= $this->include('templates/header'); ?>

<!-- // $this->include('chats');  -->

<?= $this->include('templates/navbar'); ?>

<!-- Main Sidebar Container -->
<?= $this->include('templates/sidebar'); ?>

<?= $this->renderSection('content'); ?>

<?= $this->include('templates/footer'); ?>