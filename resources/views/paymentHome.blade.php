<style>
    .container {
        margin-top: 20px;
        width: 50%;
    }

    .card {
        border: 1px solid #ddd;
        border-radius: 5px;
        padding: 20px;
    }

    .card-header {
        background-color: #007bff;
        color: #fff;
    }

    .alert-success {
        background-color: #d4edda;
        border-color: #c3e6cb;
        color: #155724;
    }

    h3 {
        margin-top: 20px;
        font-size: 24px;
    }

    button {
        background-color: #007bff;
        color: #fff;
        border: none;
        padding: 10px 20px;
        margin-top: 10px;
        cursor: pointer;
        border-radius: 5px;
    }

    button:hover {
        background-color: #0056b3;
    }
</style>    




<div class="container">
<div class="row justify-content-center">
<div class="col-md-8">
<div class="card">
<div class="card-body">
<h3>Products</h3>
<a href="{{route('goToPayment', ['search list', 25])}}"><button>In Recommended Search List for $25</button></a> &nbsp;
<a href="{{route('goToPayment', ['update information', 10])}}"><button>Update Information for $10</button></a>
</div>
</div>
</div>
</div>
</div>