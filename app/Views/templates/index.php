<?= $this->include('templates/header'); ?>

<!-- // $this->include('chats');  -->

<?= $this->include('templates/navbar'); ?>

<!-- Main Sidebar Container -->
<?= $this->include('templates/sideba'); ?>

<?= $this->renderSection('content'); ?>

<?= $this->include('templates/footer'); ?>