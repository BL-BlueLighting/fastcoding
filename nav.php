        <nav class="navbar navbar-expand-lg fastcoding-navbar">
            <div class="container-fluid">
                <a class="navbar-brand fastcoding-brand" href="#">
                    <?php echo $OJ_NAME?>
                </a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarSupportedContent">
                    <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                        <li class="nav-item">
                            <a class="nav-link fastcoding-nav-link" href="<?php echo $WHERE_IS_FASTCODING; ?>/index.php">
                                <i class="fas fa-home"></i> 首页
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link fastcoding-nav-link" href="<?php echo $WHERE_IS_FASTCODING; ?>/gaming/join.php">
                                <i class="fas fa-gamepad"></i> 加入游戏
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link fastcoding-nav-link" href="<?php echo $WHERE_IS_FASTCODING; ?>/gaming/join.php">
                                <i class="fas fa-trophy"></i> 排行榜
                            </a>
                        </li>
                        <li class="nav-item">
<<<<<<< HEAD
                            <a class="nav-link fastcoding-nav-link" href="<?php echo $WHERE_IS_FASTCODING; ?>/all_fastcodings.php">
                                <i class="fas fa-list"></i> 所有游戏
                            </a>
                        </li>
                        <li class="nav-item">
=======
>>>>>>> c0956bc7d8954c7e12687e4be2f7b701dd92b056
                            <a class="nav-link fastcoding-nav-link" href="/">
                                <i class="fas fa-arrow-left"></i> 返回 OJ
                            </a>
                        </li>
                    </ul>
                    <ul class="navbar-nav mb-2 mb-lg-0">
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle fastcoding-nav-link" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="fas fa-user-circle"></i> <?php echo $un;?>
                            </a>
                            <ul class="dropdown-menu fastcoding-dropdown">
                                <li><a class="dropdown-item" href="#">
                                    <i class="fas fa-trophy"></i> 这里还啥都没有...
                                </a></li>
                            </ul>
                        </li>
                    </ul>
                </div>
            </div>
        </nav>
        
        <style>
        /* 导航栏样式 */
        .fastcoding-navbar {
            background: rgba(255, 255, 255, 0.95) !important;
            backdrop-filter: blur(10px);
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
            padding: 12px 0;
            border-bottom: 1px solid rgba(102, 126, 234, 0.1);
        }
        
        .fastcoding-brand {
            font-size: 1.5rem;
            font-weight: 700;
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            padding: 5px 0;
        }
        
        .fastcoding-nav-link {
            color: #555 !important;
            font-weight: 500;
            padding: 8px 16px !important;
            margin: 0 5px;
            border-radius: 8px;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        
        .fastcoding-nav-link:hover {
            color: var(--primary-color) !important;
            background: rgba(102, 126, 234, 0.1);
            transform: translateY(-2px);
        }
        
        .fastcoding-nav-link i {
            font-size: 1rem;
            width: 20px;
            text-align: center;
        }
        
        .fastcoding-dropdown {
            background: rgba(255, 255, 255, 0.98);
            backdrop-filter: blur(10px);
            border: none;
            border-radius: 12px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.15);
            padding: 10px 0;
            margin-top: 10px;
        }
        
        .fastcoding-dropdown .dropdown-item {
            padding: 10px 20px;
            color: #555;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .fastcoding-dropdown .dropdown-item:hover {
            background: rgba(102, 126, 234, 0.1);
            color: var(--primary-color);
        }
        
        .navbar-toggler {
            border: none;
            padding: 5px 10px;
        }
        
        .navbar-toggler:focus {
            box-shadow: none;
        }
        
        .navbar-toggler-icon {
            background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 30 30'%3e%3cpath stroke='rgba%28102, 126, 234, 1%29' stroke-linecap='round' stroke-miterlimit='10' stroke-width='2' d='M4 7h22M4 15h22M4 23h22'/%3e%3c/svg%3e");
        }
        
        /* 响应式调整 */
        @media (max-width: 991px) {
            .navbar-collapse {
                background: rgba(255, 255, 255, 0.98);
                backdrop-filter: blur(10px);
                border-radius: 12px;
                padding: 15px;
                margin-top: 10px;
                box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            }
            
            .fastcoding-nav-link {
                margin: 5px 0;
                justify-content: flex-start;
            }
        }
        </style>
        
        <!-- 添加 Font Awesome 图标库 -->
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">