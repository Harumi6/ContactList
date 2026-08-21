<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * @var string|null $page_title
 * @var string|null $extra_css
 */
$title = !empty($page_title) ? $page_title : 'ATTG Contact';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="icon" type="image/png" href="<?= base_url('assets/icon-images/logo.png') ?>">
  <title><?= html_escape($title) ?></title>

  <!-- Google Font: Source Sans Pro -->
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
  <!-- Font Awesome Icons -->
  <link rel="stylesheet" href="<?= base_url('assets/css/all.min.css') ?>">
  <link rel="stylesheet" href="<?= base_url('assets/css/font-awesome.css') ?>">
  <!-- AdminLTE Theme style -->
  <link rel="stylesheet" href="<?= base_url('assets/css/adminlte.min.css') ?>">

  <?php if (!empty($extra_css)): ?>
    <?= $extra_css ?>
  <?php endif; ?>
</head>
