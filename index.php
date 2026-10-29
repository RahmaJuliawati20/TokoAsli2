<?php

session_start();

include "config/koneksi.php";
include "config/auth.php";

wajib_login();


/* =========================================================
   DATA USER LOGIN
========================================================= */

$nama_lengkap = $_SESSION['nama_lengkap'] ?? 'User';
$level        = $_SESSION['level'] ?? '';


/* =========================================================
   HELPER
========================================================= */

function rupiah($angka)
{
    return 'Rp ' . number_format(
        (float) $angka,
        0,
        ',',
        '.'
    );
}

function angka($angka)
{
    return number_format(
        (float) $angka,
        0,
        ',',
        '.'
    );
}


/* =========================================================
   DATA DASHBOARD
========================================================= */


/* TOTAL JENIS BARANG */

$query_barang = mysqli_query(
    $conn,
    "SELECT COUNT(*) AS total FROM barang"
);

$data_barang = mysqli_fetch_assoc($query_barang);

$total_barang = (int) (
    $data_barang['total'] ?? 0
);


/* TOTAL SUPPLIER */

$query_supplier = mysqli_query(
    $conn,
    "SELECT COUNT(*) AS total FROM supplier"
);

$data_supplier = mysqli_fetch_assoc($query_supplier);

$total_supplier = (int) (
    $data_supplier['total'] ?? 0
);


/* TOTAL PEMBELIAN */

$query_pembelian = mysqli_query(
    $conn,
    "SELECT COUNT(*) AS total FROM pembelian"
);

$data_pembelian = mysqli_fetch_assoc($query_pembelian);

$total_pembelian = (int) (
    $data_pembelian['total'] ?? 0
);


/* TOTAL STOK */

$query_stok = mysqli_query(
    $conn,
    "SELECT COALESCE(SUM(stok), 0) AS total
     FROM barang"
);

$data_stok = mysqli_fetch_assoc($query_stok);

$total_stok = (int) (
    $data_stok['total'] ?? 0
);


/* TOTAL STOK HABIS */

$query_habis = mysqli_query(
    $conn,
    "SELECT COUNT(*) AS total
     FROM barang
     WHERE stok <= 0"
);

$data_habis = mysqli_fetch_assoc($query_habis);

$total_habis = (int) (
    $data_habis['total'] ?? 0
);


/* =========================================================
   GRAFIK PEMBELIAN 6 BULAN TERAKHIR
========================================================= */

$chart_labels = [];
$chart_values = [];

for ($i = 5; $i >= 0; $i--) {

    $timestamp = strtotime("-$i month");

    $bulan_nama = date('M', $timestamp);
    $tahun      = date('Y', $timestamp);
    $bulan      = date('m', $timestamp);

    $chart_labels[] = $bulan_nama;

    $query_chart = mysqli_query(
        $conn,
        "SELECT COALESCE(SUM(total_pembelian), 0) AS total
         FROM pembelian
         WHERE YEAR(tanggal_pembelian) = '$tahun'
         AND MONTH(tanggal_pembelian) = '$bulan'"
    );

    $data_chart = mysqli_fetch_assoc($query_chart);

    $chart_values[] = (float) (
        $data_chart['total'] ?? 0
    );
}

$max_chart = max($chart_values);

if ($max_chart <= 0) {
    $max_chart = 1;
}


/* =========================================================
   PEMBELIAN TERBARU
========================================================= */

$query_terbaru = mysqli_query(
    $conn,
    "SELECT
        p.id_pembelian,
        p.tanggal_pembelian,
        p.total_pembelian,
        s.nama_supplier
     FROM pembelian p
     LEFT JOIN supplier s
        ON p.id_supplier = s.id_supplier
     ORDER BY p.id_pembelian DESC
     LIMIT 5"
);


/* =========================================================
   STOK MENIPIS
========================================================= */

$query_stok_menipis = mysqli_query(
    $conn,
    "SELECT
        id_barang,
        nama_barang,
        stok,
        satuan
     FROM barang
     WHERE stok <= 10
     ORDER BY stok ASC, nama_barang ASC
     LIMIT 5"
);

?>

<!DOCTYPE html>

<html lang="id">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>
        Dashboard - Toko Bangunan ASLI 2
    </title>


    <style>

        /* =====================================================
           RESET
        ===================================================== */

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }


        html {
            scroll-behavior: smooth;
        }


        body {

            font-family:
                "Segoe UI",
                Arial,
                Helvetica,
                sans-serif;

            background: #f4f7fb;

            color: #172033;

            font-size: 14px;

            line-height: 1.5;

        }


        a {
            text-decoration: none;
            color: inherit;
        }


        button,
        input,
        select,
        textarea {
            font-family: inherit;
        }


        /* =====================================================
           LAYOUT
        ===================================================== */

        .wrapper {

            min-height: 100vh;

            display: flex;

        }


        /* =====================================================
           SIDEBAR
        ===================================================== */

        .sidebar {

            width: 250px;

            min-height: 100vh;

            position: fixed;

            left: 0;
            top: 0;
            bottom: 0;

            background: #111827;

            color: white;

            display: flex;

            flex-direction: column;

            z-index: 100;

            box-shadow:
                4px 0 20px
                rgba(15, 23, 42, .08);

        }


        /* BRAND */

        .sidebar-header {

            padding: 22px 20px;

            border-bottom:
                1px solid
                rgba(255,255,255,.08);

        }


        .brand {

            display: flex;

            align-items: center;

            gap: 12px;

        }


        .brand-logo {

            width: 44px;
            height: 44px;

            flex-shrink: 0;

            border-radius: 12px;

            background:
                linear-gradient(
                    135deg,
                    #2563eb,
                    #1d4ed8
                );

            display: flex;

            align-items: center;
            justify-content: center;

            color: white;

            font-size: 15px;

            font-weight: 800;

            box-shadow:
                0 6px 15px
                rgba(37,99,235,.25);

        }


        .brand-text h2 {

            font-size: 16px;

            color: #ffffff;

            font-weight: 700;

            line-height: 1.2;

        }


        .brand-text p {

            color: #94a3b8;

            font-size: 11px;

            margin-top: 3px;

        }


        /* USER */

        .sidebar-user {

            margin: 18px 14px 10px;

            padding: 13px 14px;

            background:
                rgba(255,255,255,.05);

            border:
                1px solid
                rgba(255,255,255,.07);

            border-radius: 11px;

        }


        .sidebar-user-name {

            color: white;

            font-size: 13px;

            font-weight: 700;

            overflow: hidden;

            white-space: nowrap;

            text-overflow: ellipsis;

        }


        .sidebar-user-role {

            color: #93c5fd;

            font-size: 11px;

            margin-top: 3px;

            text-transform: capitalize;

        }


        /* MENU */

        .sidebar-menu {

            padding: 12px;

            overflow-y: auto;

        }


        .menu-title {

            color: #64748b;

            font-size: 10px;

            font-weight: 800;

            text-transform: uppercase;

            letter-spacing: 1px;

            padding: 8px 12px 10px;

        }


        .menu-item {

            display: flex;

            align-items: center;

            gap: 11px;

            width: 100%;

            padding: 11px 13px;

            margin-bottom: 4px;

            border-radius: 9px;

            color: #cbd5e1;

            font-size: 13px;

            font-weight: 500;

            transition: .2s ease;

        }


        .menu-item:hover {

            background:
                rgba(255,255,255,.07);

            color: white;

            transform: translateX(2px);

        }


        .menu-item.active {

            background:
                linear-gradient(
                    135deg,
                    #2563eb,
                    #1d4ed8
                );

            color: white;

            font-weight: 600;

            box-shadow:
                0 5px 15px
                rgba(37,99,235,.25);

        }


        .menu-icon {

            width: 25px;

            height: 25px;

            display: flex;

            align-items: center;
            justify-content: center;

            font-size: 16px;

            flex-shrink: 0;

        }


        /* =====================================================
           MAIN
        ===================================================== */

        .main {

            margin-left: 250px;

            width:
                calc(100% - 250px);

            min-height: 100vh;

        }


        /* =====================================================
           TOPBAR
        ===================================================== */

        .topbar {

            height: 76px;

            background: white;

            border-bottom:
                1px solid #e5e7eb;

            display: flex;

            align-items: center;

            justify-content: space-between;

            padding: 0 30px;

            position: sticky;

            top: 0;

            z-index: 50;

        }


        .topbar-title h1 {

            color: #111827;

            font-size: 20px;

            font-weight: 700;

            line-height: 1.2;

        }


        .topbar-title p {

            color: #64748b;

            font-size: 12px;

            margin-top: 4px;

        }


        .user-box {

            display: flex;

            align-items: center;

            gap: 11px;

        }


        .user-info {

            text-align: right;

        }


        .user-info strong {

            display: block;

            color: #111827;

            font-size: 13px;

            font-weight: 700;

        }


        .user-info span {

            display: block;

            color: #64748b;

            font-size: 11px;

            margin-top: 1px;

            text-transform: capitalize;

        }


        .user-avatar {

            width: 40px;
            height: 40px;

            border-radius: 50%;

            background: #dbeafe;

            color: #2563eb;

            display: flex;

            align-items: center;
            justify-content: center;

            font-size: 14px;

            font-weight: 800;

            border:
                3px solid
                #eff6ff;

        }


        /* =====================================================
           CONTENT
        ===================================================== */

        .content {

            padding: 28px 30px 35px;

            max-width: 1600px;

            margin: 0 auto;

        }


        /* =====================================================
           WELCOME
        ===================================================== */

        .welcome {

            position: relative;

            overflow: hidden;

            background:
                linear-gradient(
                    135deg,
                    #2563eb 0%,
                    #1d4ed8 100%
                );

            color: white;

            border-radius: 16px;

            padding: 25px 28px;

            margin-bottom: 22px;

            display: flex;

            align-items: center;

            justify-content: space-between;

            box-shadow:
                0 10px 25px
                rgba(37,99,235,.16);

        }


        .welcome::after {

            content: "";

            position: absolute;

            width: 180px;
            height: 180px;

            right: -60px;
            top: -70px;

            border-radius: 50%;

            background:
                rgba(255,255,255,.08);

        }


        .welcome h2 {

            position: relative;

            z-index: 1;

            font-size: 21px;

            font-weight: 700;

            margin-bottom: 6px;

        }


        .welcome p {

            position: relative;

            z-index: 1;

            font-size: 13px;

            color:
                rgba(255,255,255,.88);

        }


        .welcome-badge {

            position: relative;

            z-index: 2;

            background:
                rgba(255,255,255,.15);

            border:
                1px solid
                rgba(255,255,255,.15);

            padding: 9px 14px;

            border-radius: 9px;

            font-size: 11px;

            font-weight: 700;

            white-space: nowrap;

        }


        /* =====================================================
           STATISTICS
        ===================================================== */

        .dashboard-stats {

            display: grid;

            grid-template-columns:
                repeat(4, minmax(0, 1fr));

            gap: 16px;

            margin-bottom: 22px;

        }


        .dashboard-stat {

            background: white;

            border:
                1px solid #e5e7eb;

            border-radius: 13px;

            padding: 18px;

            display: flex;

            align-items: center;

            gap: 14px;

            transition: .2s ease;

            box-shadow:
                0 2px 8px
                rgba(15,23,42,.025);

        }


        .dashboard-stat:hover {

            transform: translateY(-2px);

            box-shadow:
                0 8px 22px
                rgba(15,23,42,.07);

        }


        .stat-icon {

            width: 46px;
            height: 46px;

            border-radius: 11px;

            display: flex;

            align-items: center;
            justify-content: center;

            font-size: 20px;

            flex-shrink: 0;

        }


        .stat-blue {

            background: #eff6ff;

            color: #2563eb;

        }


        .stat-green {

            background: #ecfdf5;

            color: #16a34a;

        }


        .stat-orange {

            background: #fff7ed;

            color: #ea580c;

        }


        .stat-red {

            background: #fef2f2;

            color: #dc2626;

        }


        .stat-content span {

            display: block;

            color: #64748b;

            font-size: 11px;

            margin-bottom: 3px;

        }


        .stat-content strong {

            display: block;

            color: #111827;

            font-size: 21px;

            font-weight: 750;

        }


        /* =====================================================
           DASHBOARD GRID
        ===================================================== */

        .dashboard-grid {

            display: grid;

            grid-template-columns:
                minmax(0, 1.75fr)
                minmax(300px, .85fr);

            gap: 18px;

            margin-bottom: 18px;

        }


        /* =====================================================
           CARD
        ===================================================== */

        .card {

            background: white;

            border:
                1px solid #e5e7eb;

            border-radius: 13px;

            overflow: hidden;

            box-shadow:
                0 2px 8px
                rgba(15,23,42,.025);

        }


        .card-header {

            padding: 17px 20px;

            border-bottom:
                1px solid #eef0f3;

            display: flex;

            align-items: center;

            justify-content: space-between;

        }


        .card-header h3 {

            color: #111827;

            font-size: 15px;

            font-weight: 700;

        }


        .card-header p {

            color: #94a3b8;

            font-size: 11px;

            margin-top: 3px;

        }


        .lihat-semua {

            color: #2563eb;

            font-size: 11px;

            font-weight: 700;

        }


        .lihat-semua:hover {

            text-decoration: underline;

        }


        /* =====================================================
           CHART
        ===================================================== */

        .chart-container {

            padding: 22px 20px 18px;

        }


        .chart {

            height: 275px;

            display: flex;

            align-items: flex-end;

            gap: 12px;

            padding:
                5px 4px 0;

            border-bottom:
                1px solid #e5e7eb;

        }


        .chart-column {

            flex: 1;

            height: 100%;

            display: flex;

            flex-direction: column;

            align-items: center;

            justify-content: flex-end;

            gap: 6px;

            min-width: 0;

        }


        .chart-value {

            color: #64748b;

            font-size: 9px;

            min-height: 15px;

            white-space: nowrap;

        }


        .chart-bar-wrapper {

            height: 215px;

            width: 100%;

            display: flex;

            align-items: flex-end;

            justify-content: center;

        }


        .chart-bar {

            width: 42px;

            max-width: 75%;

            min-height: 4px;

            background:
                linear-gradient(
                    180deg,
                    #3b82f6,
                    #2563eb
                );

            border-radius:
                7px 7px 2px 2px;

            transition:
                .25s ease;

        }


        .chart-bar:hover {

            background:
                linear-gradient(
                    180deg,
                    #60a5fa,
                    #1d4ed8
                );

            transform:
                scaleX(1.06);

        }


        .chart-label {

            color: #64748b;

            font-size: 11px;

            font-weight: 500;

            margin-top: 4px;

        }


        /* =====================================================
           STOCK
        ===================================================== */

        .stock-list {

            padding: 4px 20px 8px;

        }


        .stock-item {

            display: flex;

            align-items: center;

            justify-content: space-between;

            gap: 15px;

            padding: 14px 0;

            border-bottom:
                1px solid #f1f5f9;

        }


        .stock-item:last-child {

            border-bottom: none;

        }


        .stock-name {

            color: #1f2937;

            font-size: 12px;

            font-weight: 700;

            margin-bottom: 3px;

        }


        .stock-unit {

            color: #94a3b8;

            font-size: 10px;

        }


        .stock-number {

            min-width: 44px;

            padding: 6px 8px;

            border-radius: 7px;

            text-align: center;

            background: #fee2e2;

            color: #dc2626;

            font-size: 11px;

            font-weight: 800;

        }


        .stock-number.warning {

            background: #ffedd5;

            color: #ea580c;

        }


        /* =====================================================
           PURCHASE
        ===================================================== */

        .purchase-list {

            padding:
                3px 20px 8px;

        }


        .purchase-item {

            display: flex;

            align-items: center;

            justify-content: space-between;

            gap: 20px;

            padding: 14px 0;

            border-bottom:
                1px solid #f1f5f9;

        }


        .purchase-item:last-child {

            border-bottom: none;

        }


        .purchase-left {

            min-width: 0;

        }


        .purchase-code {

            color: #111827;

            font-size: 12px;

            font-weight: 750;

            margin-bottom: 3px;

        }


        .purchase-supplier {

            color: #94a3b8;

            font-size: 10px;

            white-space: nowrap;

            overflow: hidden;

            text-overflow: ellipsis;

            max-width: 350px;

        }


        .purchase-right {

            text-align: right;

            flex-shrink: 0;

        }


        .purchase-total {

            color: #2563eb;

            font-size: 12px;

            font-weight: 750;

            margin-bottom: 2px;

        }


        .purchase-date {

            color: #94a3b8;

            font-size: 10px;

        }


        /* =====================================================
           QUICK MENU
        ===================================================== */

        .quick-section {

            margin-top: 22px;

        }


        .section-title {

            color: #111827;

            font-size: 16px;

            font-weight: 700;

            margin-bottom: 13px;

        }


        .quick-grid {

            display: grid;

            grid-template-columns:
                repeat(4, minmax(0, 1fr));

            gap: 14px;

        }


        .quick-card {

            background: white;

            border:
                1px solid #e5e7eb;

            border-radius: 12px;

            padding: 17px;

            transition: .2s ease;

        }


        .quick-card:hover {

            transform: translateY(-2px);

            border-color: #bfdbfe;

            box-shadow:
                0 8px 20px
                rgba(15,23,42,.06);

        }


        .quick-card-icon {

            width: 40px;
            height: 40px;

            border-radius: 10px;

            background: #eff6ff;

            display: flex;

            align-items: center;
            justify-content: center;

            font-size: 18px;

            margin-bottom: 11px;

        }


        .quick-card h4 {

            color: #1f2937;

            font-size: 13px;

            font-weight: 700;

            margin-bottom: 4px;

        }


        .quick-card p {

            color: #94a3b8;

            font-size: 10px;

            line-height: 1.5;

        }


        /* =====================================================
           EMPTY STATE
        ===================================================== */

        .empty-state {

            text-align: center;

            padding: 30px 15px;

            color: #94a3b8;

            font-size: 12px;

        }


        /* =====================================================
           FOOTER
        ===================================================== */

        .footer {

            text-align: center;

            color: #94a3b8;

            font-size: 10px;

            padding:
                28px 0 8px;

        }


        /* =====================================================
           RESPONSIVE 1100
        ===================================================== */

        @media (max-width: 1100px) {

            .dashboard-stats {

                grid-template-columns:
                    repeat(2, minmax(0, 1fr));

            }


            .quick-grid {

                grid-template-columns:
                    repeat(2, minmax(0, 1fr));

            }

        }


        /* =====================================================
           RESPONSIVE 900
        ===================================================== */

        @media (max-width: 900px) {

            .sidebar {

                width: 220px;

            }


            .main {

                margin-left: 220px;

                width:
                    calc(100% - 220px);

            }


            .dashboard-grid {

                grid-template-columns: 1fr;

            }


            .content {

                padding: 22px;

            }


            .topbar {

                padding:
                    0 22px;

            }

        }


        /* =====================================================
           RESPONSIVE 700
        ===================================================== */

        @media (max-width: 700px) {

            .wrapper {

                display: block;

            }


            .sidebar {

                position: relative;

                width: 100%;

                min-height: auto;

            }


            .sidebar-menu {

                padding-bottom: 15px;

            }


            .main {

                margin-left: 0;

                width: 100%;

            }


            .topbar {

                height: auto;

                padding: 15px 20px;

            }


            .user-info {

                display: none;

            }


            .content {

                padding: 18px;

            }


            .welcome {

                display: block;

                padding: 22px;

            }


            .welcome-badge {

                display: inline-block;

                margin-top: 14px;

            }


            .dashboard-stats {

                grid-template-columns: 1fr;

            }


            .quick-grid {

                grid-template-columns: 1fr;

            }


            .chart {

                height: 240px;

            }


            .chart-bar-wrapper {

                height: 180px;

            }

        }


        /* =====================================================
           RESPONSIVE 450
        ===================================================== */

        @media (max-width: 450px) {

            .topbar-title h1 {

                font-size: 17px;

            }


            .topbar-title p {

                font-size: 10px;

            }


            .welcome h2 {

                font-size: 18px;

            }


            .welcome p {

                font-size: 11px;

            }


            .chart-value {

                font-size: 8px;

            }


            .chart-label {

                font-size: 9px;

            }


            .purchase-item {

                gap: 10px;

            }


            .purchase-supplier {

                max-width: 160px;

            }

        }

    </style>

</head>


<body>


<div class="wrapper">


    <!-- =====================================================
         SIDEBAR
    ====================================================== -->

    <aside class="sidebar">


        <!-- BRAND -->

        <div class="sidebar-header">

            <div class="brand">

                <div class="brand-logo">
                    A2
                </div>

                <div class="brand-text">

                    <h2>
                        ASLI 2
                    </h2>

                    <p>
                        Toko Bangunan
                    </p>

                </div>

            </div>

        </div>


        <!-- USER -->

        <div class="sidebar-user">

            <div class="sidebar-user-name">

                <?= e($nama_lengkap); ?>

            </div>

            <div class="sidebar-user-role">

                <?= e(ucfirst($level)); ?>

            </div>

        </div>


        <!-- MENU -->

        <div class="sidebar-menu">


            <div class="menu-title">
                Menu Utama
            </div>


            <!-- DASHBOARD -->

            <a
                href="index.php"
                class="menu-item active"
            >

                <span class="menu-icon">
                    🏠
                </span>

                <span>
                    Dashboard
                </span>

            </a>


            <?php if (punya_role('admin')): ?>


                <!-- BARANG -->

                <a
                    href="pages/barang/index.php"
                    class="menu-item"
                >

                    <span class="menu-icon">
                        📦
                    </span>

                    <span>
                        Data Barang
                    </span>

                </a>


                <!-- SUPPLIER -->

                <a
                    href="pages/supplier/index.php"
                    class="menu-item"
                >

                    <span class="menu-icon">
                        🚚
                    </span>

                    <span>
                        Data Supplier
                    </span>

                </a>


                <!-- PEMBELIAN -->

                <a
                    href="pages/pembelian/index.php"
                    class="menu-item"
                >

                    <span class="menu-icon">
                        🛒
                    </span>

                    <span>
                        Pembelian
                    </span>

                </a>


                            <a href="pages/penerimaan/index.php" class="menu-item">
                    <span class="menu-icon">📥</span>
                    <span>Penerimaan Barang</span>
                </a>


            <?php endif; ?>


            <?php if (
                punya_role('admin') ||
                punya_role('pemilik')
            ): ?>


                <!-- PERSEDIAAN -->

                <a
                    href="pages/persediaan/index.php"
                    class="menu-item"
                >

                    <span class="menu-icon">
                        📊
                    </span>

                    <span>
                        Persediaan
                    </span>

                </a>


                <!-- LAPORAN -->

                <a
                    href="pages/laporan/index.php"
                    class="menu-item"
                >

                    <span class="menu-icon">
                        📄
                    </span>

                    <span>
                        Laporan Pembelian
                    </span>

                </a>


            <?php endif; ?>


            <?php if (punya_role('pemilik')): ?>


                <!-- USER -->

                <a
                    href="pages/user/index.php"
                    class="menu-item"
                >

                    <span class="menu-icon">
                        👤
                    </span>

                    <span>
                        Tambah User
                    </span>

                </a>


            <?php endif; ?>


            <!-- LOGOUT -->

            <a
                href="logout.php"
                class="menu-item"
                onclick="
                    return confirm(
                        'Apakah Anda yakin ingin keluar?'
                    );
                "
            >

                <span class="menu-icon">
                    🚪
                </span>

                <span>
                    Logout
                </span>

            </a>


        </div>

    </aside>


    <!-- =====================================================
         MAIN
    ====================================================== -->

    <main class="main">


        <!-- TOPBAR -->

        <header class="topbar">


            <div class="topbar-title">

                <h1>
                    Dashboard
                </h1>

                <p>
                    Sistem Informasi Pembelian Barang
                </p>

            </div>


            <div class="user-box">


                <div class="user-info">

                    <strong>
                        <?= e($nama_lengkap); ?>
                    </strong>

                    <span>
                        <?= e($level); ?>
                    </span>

                </div>


                <div class="user-avatar">

                    <?= strtoupper(
                        substr(
                            $nama_lengkap,
                            0,
                            1
                        )
                    ); ?>

                </div>


            </div>


        </header>


        <!-- =================================================
             CONTENT
        ================================================== -->

        <div class="content">


            <!-- =================================================
                 WELCOME
            ================================================== -->

            <section class="welcome">


                <div>

                    <h2>

                        Selamat Datang,
                        <?= e($nama_lengkap); ?>!

                    </h2>


                    <p>

                        Kelola data barang, supplier,
                        pembelian, persediaan dan laporan
                        Toko Bangunan ASLI 2.

                    </p>

                </div>


                <div class="welcome-badge">

                    Login sebagai
                    <?= e($level); ?>

                </div>


            </section>


            <!-- =================================================
                 STATISTIK
            ================================================== -->

            <section class="dashboard-stats">


                <!-- BARANG -->

                <div class="dashboard-stat">

                    <div class="stat-icon stat-blue">
                        📦
                    </div>

                    <div class="stat-content">

                        <span>
                            Total Jenis Barang
                        </span>

                        <strong>
                            <?= angka($total_barang); ?>
                        </strong>

                    </div>

                </div>


                <!-- SUPPLIER -->

                <div class="dashboard-stat">

                    <div class="stat-icon stat-green">
                        🚚
                    </div>

                    <div class="stat-content">

                        <span>
                            Total Supplier
                        </span>

                        <strong>
                            <?= angka($total_supplier); ?>
                        </strong>

                    </div>

                </div>


                <!-- PEMBELIAN -->

                <div class="dashboard-stat">

                    <div class="stat-icon stat-orange">
                        🛒
                    </div>

                    <div class="stat-content">

                        <span>
                            Total Pembelian
                        </span>

                        <strong>
                            <?= angka($total_pembelian); ?>
                        </strong>

                    </div>

                </div>


                <!-- STOK -->

                <div class="dashboard-stat">

                    <div class="stat-icon stat-red">
                        📊
                    </div>

                    <div class="stat-content">

                        <span>
                            Total Stok
                        </span>

                        <strong>
                            <?= angka($total_stok); ?>
                        </strong>

                    </div>

                </div>


            </section>


            <!-- =================================================
                 GRAFIK + STOK
            ================================================== -->

            <section class="dashboard-grid">


                <!-- GRAFIK -->

                <div class="card">


                    <div class="card-header">

                        <div>

                            <h3>
                                Grafik Pembelian
                            </h3>

                            <p>
                                Total pembelian 6 bulan terakhir
                            </p>

                        </div>

                        <span class="lihat-semua">
                            6 Bulan
                        </span>

                    </div>


                    <div class="chart-container">


                        <div class="chart">


                            <?php foreach (
                                $chart_values
                                as $index => $value
                            ): ?>


                                <?php

                                $height =
                                    ($value / $max_chart) * 100;

                                if (
                                    $value > 0 &&
                                    $height < 4
                                ) {

                                    $height = 4;

                                }

                                ?>


                                <div class="chart-column">


                                    <div class="chart-value">

                                        <?= rupiah($value); ?>

                                    </div>


                                    <div class="chart-bar-wrapper">


                                        <div
                                            class="chart-bar"
                                            style="
                                                height:
                                                <?= $height; ?>%;
                                            "
                                            title="
                                                <?= rupiah($value); ?>
                                            "
                                        ></div>


                                    </div>


                                    <div class="chart-label">

                                        <?= e(
                                            $chart_labels[$index]
                                        ); ?>

                                    </div>


                                </div>


                            <?php endforeach; ?>


                        </div>


                    </div>


                </div>


                <!-- STOK MENIPIS -->

                <div class="card">


                    <div class="card-header">

                        <div>

                            <h3>
                                Stok Menipis
                            </h3>

                            <p>
                                Barang dengan stok ≤ 10
                            </p>

                        </div>

                        <span class="lihat-semua">

                            <?= angka($total_habis); ?>

                            habis

                        </span>

                    </div>


                    <div class="stock-list">


                        <?php if (
                            mysqli_num_rows(
                                $query_stok_menipis
                            ) > 0
                        ): ?>


                            <?php while (
                                $stok =
                                    mysqli_fetch_assoc(
                                        $query_stok_menipis
                                    )
                            ): ?>


                                <div class="stock-item">


                                    <div>

                                        <div class="stock-name">

                                            <?= e(
                                                $stok['nama_barang']
                                            ); ?>

                                        </div>


                                        <div class="stock-unit">

                                            Satuan:
                                            <?= e(
                                                $stok['satuan']
                                            ); ?>

                                        </div>

                                    </div>


                                    <div
                                        class="
                                            stock-number
                                            <?php
                                            if (
                                                $stok['stok'] > 0
                                            ) {
                                                echo 'warning';
                                            }
                                            ?>
                                        "
                                    >

                                        <?= angka(
                                            $stok['stok']
                                        ); ?>

                                    </div>


                                </div>


                            <?php endwhile; ?>


                        <?php else: ?>


                            <div class="empty-state">

                                Semua stok barang masih aman.

                            </div>


                        <?php endif; ?>


                    </div>


                </div>


            </section>


            <!-- =================================================
                 PEMBELIAN TERBARU
            ================================================== -->

            <section class="card">


                <div class="card-header">


                    <div>

                        <h3>
                            Pembelian Terbaru
                        </h3>

                        <p>
                            5 transaksi pembelian terakhir
                        </p>

                    </div>


                    <a
                        href="pages/laporan/index.php"
                        class="lihat-semua"
                    >
                        Lihat Laporan →
                    </a>


                </div>


                <div class="purchase-list">


                    <?php if (
                        mysqli_num_rows(
                            $query_terbaru
                        ) > 0
                    ): ?>


                        <?php while (
                            $pembelian =
                                mysqli_fetch_assoc(
                                    $query_terbaru
                                )
                        ): ?>


                            <div class="purchase-item">


                                <div class="purchase-left">


                                    <div class="purchase-code">

                                        PB
                                        <?= str_pad(
                                            $pembelian[
                                                'id_pembelian'
                                            ],
                                            4,
                                            '0',
                                            STR_PAD_LEFT
                                        ); ?>

                                    </div>


                                    <div class="purchase-supplier">

                                        <?= e(
                                            $pembelian[
                                                'nama_supplier'
                                            ] ??
                                            'Supplier tidak ditemukan'
                                        ); ?>

                                    </div>


                                </div>


                                <div class="purchase-right">


                                    <div class="purchase-total">

                                        <?= rupiah(
                                            $pembelian[
                                                'total_pembelian'
                                            ]
                                        ); ?>

                                    </div>


                                    <div class="purchase-date">

                                        <?= date(
                                            'd-m-Y',
                                            strtotime(
                                                $pembelian[
                                                    'tanggal_pembelian'
                                                ]
                                            )
                                        ); ?>

                                    </div>


                                </div>


                            </div>


                        <?php endwhile; ?>


                    <?php else: ?>


                        <div class="empty-state">

                            Belum ada transaksi pembelian.

                        </div>


                    <?php endif; ?>


                </div>


            </section>


            <!-- =================================================
                 MENU CEPAT
            ================================================== -->

            <section class="quick-section">


                <h3 class="section-title">
                    Menu Cepat
                </h3>


                <div class="quick-grid">


                    <?php if (punya_role('admin')): ?>


                        <!-- TAMBAH BARANG -->

                        <a
                            href="pages/barang/tambah.php"
                            class="quick-card"
                        >

                            <div class="quick-card-icon">
                                📦
                            </div>

                            <h4>
                                Tambah Barang
                            </h4>

                            <p>
                                Tambahkan data barang baru
                                ke dalam sistem.
                            </p>

                        </a>


                        <!-- TAMBAH SUPPLIER -->

                        <a
                            href="pages/supplier/tambah.php"
                            class="quick-card"
                        >

                            <div class="quick-card-icon">
                                🚚
                            </div>

                            <h4>
                                Tambah Supplier
                            </h4>

                            <p>
                                Tambahkan supplier baru
                                ke dalam sistem.
                            </p>

                        </a>


                        <!-- TAMBAH PEMBELIAN -->

                        <a
                            href="pages/pembelian/tambah.php"
                            class="quick-card"
                        >

                            <div class="quick-card-icon">
                                🛒
                            </div>

                            <h4>
                                Tambah Pembelian
                            </h4>

                            <p>
                                Catat transaksi pembelian
                                barang.
                            </p>

                        </a>


                    <?php endif; ?>


                    <!-- PERSEDIAAN -->

                    <a
                        href="pages/persediaan/index.php"
                        class="quick-card"
                    >

                        <div class="quick-card-icon">
                            📊
                        </div>

                        <h4>
                            Persediaan
                        </h4>

                        <p>
                            Pantau jumlah stok barang
                            yang tersedia.
                        </p>

                    </a>


                    <!-- LAPORAN -->

                    <a
                        href="pages/laporan/index.php"
                        class="quick-card"
                    >

                        <div class="quick-card-icon">
                            📄
                        </div>

                        <h4>
                            Laporan Pembelian
                        </h4>

                        <p>
                            Lihat dan cetak laporan
                            pembelian barang.
                        </p>

                    </a>


                    <?php if (punya_role('pemilik')): ?>


                        <!-- USER -->

                        <a
                            href="pages/user/index.php"
                            class="quick-card"
                        >

                            <div class="quick-card-icon">
                                👤
                            </div>

                            <h4>
                                Tambah User
                            </h4>

                            <p>
                                Kelola akun admin dan
                                pemilik sistem.
                            </p>

                        </a>


                    <?php endif; ?>


                </div>


            </section>


            <!-- =================================================
                 FOOTER
            ================================================== -->

            <footer class="footer">

                &copy;
                <?= date('Y'); ?>

                Toko Bangunan ASLI 2.

                Sistem Informasi Pembelian Barang.

            </footer>


        </div>


    </main>


</div>


</body>

</html>