<nav class="navbar navbar-expand-lg navbar-light">
    <div class="container-fluid">
        <a class="navbar-brand text-white" href="#" id="navbarTitle" ></a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav"
            aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse justify-content-start" id="navbarNav">
            <ul class="navbar-nav">
                <li class="nav-item">
                    <a class="nav-link text-white" href="/products"
                        data-page="products">Products</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link text-white" href="/customers"
                        data-page="customers">Customers</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link text-white" href="/users" data-page="users">Users</a>
                </li>
            </ul>
        </div>

        <div class="d-flex align-items-center">
                <span class="navbar-text mr-3 text-white">👤
                    {{ session('user.group_role', Auth::user()->group_role ?? 'N/A') }}
                </span>
                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit" class="btn btn-danger">Logout</button>
                </form>
        </div>
    </div>
</nav>

<style>
    .navbar {
        background-color: #17a2b8;
        margin-bottom: 10px;
    }

    .nav-item.active .nav-link {
        background-color: #dc3545;
        border-radius: 5px;
    }
</style>

<script>
    document.addEventListener("DOMContentLoaded", function () {
        const currentPage = window.location.pathname.split("/").pop().split(".")[0]; // ví dụ: user từ "user.action"
        console.log(currentPage);
        
        const pageTitles = {
            "users": "QUẢN LÝ USER",
            "products": "QUẢN LÝ SẢN PHẨM",
            "customers": "QUẢN LÝ KHÁCH HÀNG"
        }

        const navbarTitle = document.getElementById("navbarTitle");
        navbarTitle.textContent = pageTitles[currentPage] || pageTitles[""]
        document.querySelectorAll('.nav-link').forEach(function (link) {
            if (link.dataset.page === currentPage) {
                link.classList.add('bg-danger', 'text-white', 'active');
            } else {
                link.classList.remove('bg-danger', 'text-white', 'active');
            }
        });


    });
</script>