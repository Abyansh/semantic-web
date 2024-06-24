<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home</title>

    <!-- CSS only -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0-beta1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-0evHe/X+R7YkIZDRvuzKMRqM+OrBnVFBL6DOitfPri4tjfHxaWutUpFmBp4vmVor" crossorigin="anonymous">

    <!-- JavaScript Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0-beta1/dist/js/bootstrap.bundle.min.js" integrity="sha384-pprn3073KE6tl6bjs2QrFaJGz5/SUsLqktiwsUTF55Jfv3qYSDhgCecCxMW52nD2" crossorigin="anonymous"></script>
    <script src="https://kit.fontawesome.com/c3c1353c4c.js" crossorigin="anonymous"></script>
</head>

<body>
    <!-- Connector untuk menghubungkan PHP dan SPARQL -->
    <?php
    require_once("sparqllib.php");
    $test = "";
    $sort = "";
    $column = "";

    if(isset($_GET['sort'])) {
        $sort = $_GET['sort'];
        $column = "ORDER BY ?$sort";
    } else {
        $column = "";
    }

    if (isset($_POST['search'])) {
        $test = $_POST['search'];
        $data = sparql_get(
            "http://localhost:3030/motogp",
            "
                prefix id: <https://MotoGP.com/> 
                prefix data: <https://MotoGP.com/ns/data#> 
                prefix rdf: <http://www.w3.org/1999/02/22-rdf-syntax-ns#> 
                
                SELECT ?racename ?birthdate ?nationality ?team
                WHERE
                { 
                    ?riders
                    data:racename      ?racename ;
                    data:birthdate     ?birthdate ;
                    data:nationality   ?nationality ;
                    data:team          ?team .
                
                    FILTER 
                    (regex(?racename, '$test', 'i') 
                    || regex(?birthdate, '$test', 'i') 
                    || regex(?nationality, '$test', 'i') 
                    || regex(?team, '$test', 'i'))
                }
            " . $column
        );
    } else {
        $data = sparql_get(
            "http://localhost:3030/motogp",
            "
                prefix id: <https://MotoGP.com/> 
                prefix data: <https://MotoGP.com/ns/data#> 
                prefix rdf: <http://www.w3.org/1999/02/22-rdf-syntax-ns#> 
                
                SELECT ?racename ?birthdate ?nationality ?team
                WHERE
                { 
                    ?riders
                    data:racename      ?racename ;
                    data:birthdate     ?birthdate ;
                    data:nationality   ?nationality ;
                    data:team          ?team .
                }
            " . $column
        );
    }

    if (!isset($data)) {
        print "<p>Error: " . sparql_errno() . ": " . sparql_error() . "</p>";
    }
    ?>

    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg bg-dark">
        <div class="container container-fluid">
            <a class="navbar-brand" href="index.php"><img src="src/img/logo-motogp.png" style="width:80px" alt="Logo"></a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0 h5">
                    <li class="nav-item px-2">
                        <a class="nav-link active text-white" aria-current="page" href="#">MotoGP Racers</a>
                    </li>
                </ul>
                <form class="d-flex" role="search" action="" method="post" id="nameform">
                    <input class="form-control me-2" type="search" placeholder="Input Keyword here" aria-label="Search" name="search">
                    <button class="btn btn-outline-success" type="submit">Search</button>
                </form>
            </div>
        </div>
    </nav>

    <div class="container container-fluid mt-3">
        <!-- Dropdown to sort -->
        <div class="dropdown">
            <button class="btn btn-secondary dropdown-toggle" type="button" id="dropdownMenuButton1" data-bs-toggle="dropdown" aria-expanded="false">
                Sort By
            </button>
            <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton1">
                <li><a class="dropdown-item" href="index.php?sort=racename">Name</a></li>
                <li><a class="dropdown-item" href="index.php?sort=birthdate">Birthdate</a></li>
                <li><a class="dropdown-item" href="index.php?sort=nationality">Nationality</a></li>
                <li><a class="dropdown-item" href="index.php?sort=team">Team</a></li>
            </ul>
        </div>

        <i class="fa-solid fa-magnifying-glass my-2 p-1"></i><span>Menampilkan hasil pencarian untuk Pembalap MotoGP <?php if ($test != "") echo "dengan keyword '$test'"; ?></span>
        <?php $i = 0; $count = 0; ?>
            <div class="container">
                <div class="row">
                    <?php foreach ($data as $dat) : ?>
                        <?php if ($count % 4 == 0 && $count != 0) : ?>
                            </div><div class="row mt-4"> <!-- Tutup row sebelumnya dan buka row baru setiap 4 card -->
                        <?php endif; ?>
                        <div class="col-md-3 mb-4">
                            <div class="card h-100">
                                <div class="card-body">
                                    <h5 class="card-title"><?= $dat['racename'] ?></h5>
                                </div>
                                <ul class="list-group list-group-flush">
                                    <li class="list-group-item"><?= date('d F Y', strtotime($dat['birthdate'])) ?></li>
                                    <li class="list-group-item"><?= $dat['nationality'] ?></li>
                                    <li class="list-group-item"><?= $dat['team'] ?></li>
                                </ul>
                            </div>
                        </div>
                        <?php $count++; ?>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php if ($count == 0) : ?>
            <p>Data tidak ditemukan</p>
        <?php endif; ?>

        <!-- Footer-->
    <footer class="bg-dark text-center text-lg-start mt-4">
        <div class="container p-4">
            <div class="row">
                <div class="col-lg-6 col-md-12 mb-4 mb-md-0">
                    <h5 class="text-white">MotoGP Data</h5>
                    <p class="text-white">Website ini menyediakan informasi tentang pembalap MotoGP dan tim mereka. Data diambil dari sumber terpercaya.</p>
                </div>

                <div class="col-lg-4 col-md-6 mb-4 mb-md-0">
                    <h5 class="text-white">Anggota</h5>
                    <ul class="list-unstyled mb-0">
                        <li>
                            <div class="text-white">Abyan Shidqi Hidayat - 140810210014</div>
                        </li>
                        <li>
                            <div class="text-white">Dimas Falah - 140810210064</div>
                        </li>
                    </ul>
                </div>    
            </div>
        </div>
</body>

</html>
