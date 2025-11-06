        <nav class="navbar navbar-expand-lg bg-body-tertiary">
            <div class="container-fluid">
                <a class="navbar-brand" href="#"><?php echo $OJ_NAME?></a>
                <div class="collapse navbar-collapse" id="navbarSupportedContent">
                    <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                        <li class="nav-item">
                            <a class="nav-link" href="<?php echo $WHERE_IS_FASTCODING; ?>index.php">首页</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="<?php echo $WHERE_IS_FASTCODING; ?>/gaming/join.php">加入</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="/">返回 OJ</a>
                        </li>
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                <?php echo $un;?>
                            </a>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item" href="<?php echo $WHERE_IS_FASTCODING; ?>ranklist.php">排行榜</a></li>
                            </ul>
                        </li>
                    </ul>
                </div>
            </div>
        </nav>