    <!DOCTYPE html>
    <html lang="en">
    <head>
        <!-- Required meta tags -->
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <!-- Bootstrap CSS -->
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">

        <title>Filter</title>
    </head>
    <body>
        <div class="container">
            @php
            echo route('apply_filters');
            @endphp

   <div class="container">
    <form action="{{route('apply_filters')}}" method="post">
        @csrf

        <div class="row mt-5">
            <div class="col-md-4">
                <select name="label" id="label" class="form-select">
                    <option value="">Select Label</option>
                    @foreach($filterData as $filter)
                        @if ($filter['type'] === 'select')
                            <option value="{{ $filter['search_in'] }}" data-operator="{{ $filter['operator'] }}" data-type="{{ $filter['id'] }}">{{ $filter['label'] }}</option>
                        @endif
                    @endforeach
                </select>
            </div>
            <div class="col-md-4">
                <div id="operator-container" style="display:none;">
                    <select name="operator" id="operator" class="form-select">
                    <option value="">Select Operator</option>
                        <option value="like">Like</option>
                        <option value="=">Equal</option>
                    </select>
                </div>
            </div>
            <div class="col-md-4">
                <div id="value-container" style="display:none;">
                    <input type="text" name="name"  class="form-control" placeholder="Enter Value">
                </div>
                <div id="email-container" style="display:none;">
                    <input type="email" name="email"  class="form-control" placeholder="Enter Value">
                </div>
                <div id="select-container" style="display:none;">
                    <select name="status" id="selectValue" class="form-select">
                    <option value="">Select Status</option>
                        <option value="active">Active</option>
                        <option value="pending">Pending</option>
                        <option value="approved">Approved</option>
                    </select>
                </div>
                 <div id="select-role" style="display:none;">
                    <select name="role" id="selectRole" class="form-select">
                    <option value="">Select Role</option>
                        <option value="admin">Admin</option>
                        <option value="user">User</option>
                    </select>
                </div>
                   <div id="sort_by" style="display:none;">
                    <select name="sort_by" id="selectRole" class="form-select">
                    <option value="">Select Sort By</option>
                        <option value="a-z">A - Z</option>
                        <option value="z-a">Z - A</option>
                    </select>
                </div>
                  <div id="sort_order" style="display:none;">
                    <select name="sort_order" id="selectRole" class="form-select">
                     <option value="">Select Order</option>
                        <option value="asc">Ascending</option>
                        <option value="dsc">Decending</option>
                    </select>
                </div>
                <div id="date-container" style="display:none;">
                    <input type="date" name="dateValue" id="dateValue" class="form-control">
                </div>
            </div>
        </div>

        <div class="row mt-3">
            <div class="col-md-12" >
                <button type="submit" class="btn btn-primary">Apply Filters</button>
            </div>
        </div>
    </form>
</div>

<script>
    var labelSelect = document.getElementById('label');
    var operatorSelect = document.getElementById('operator-container');
    var valueContainer = document.getElementById('value-container');
    var emailContainer = document.getElementById('email-container');
    var selectContainer = document.getElementById('select-container');
    var selectRole = document.getElementById('select-role');
    var dateContainer = document.getElementById('date-container');
    var sortBy = document.getElementById('sort_by');
    var sortOrder = document.getElementById('sort_order');

    labelSelect.addEventListener('change', function () {
        var selectedOption = this.options[this.selectedIndex];
        var selectedOperator = selectedOption.getAttribute('data-operator');
        var selectedType = selectedOption.getAttribute('data-type');
        operatorSelect.value = selectedOperator;
        if(selectedType === 'user_name'){
            valueContainer.style.display = 'block';
            operatorSelect.style.display = 'block';
            emailContainer.style.display = 'none';
            selectContainer.style.display = 'none';
            selectRole.style.display = 'none';
            dateContainer.style.display = 'none';
            sortBy.style.display = 'none';
            sortOrder.style.display = 'none';
        } else if(selectedType === 'email') {
            emailContainer.style.display = 'block';
            operatorSelect.style.display = 'block';
            selectContainer.style.display = 'none';
            selectRole.style.display = 'none';
            dateContainer.style.display = 'none';
            sortBy.style.display = 'none';
            sortOrder.style.display = 'none';
            valueContainer.style.display = 'none';
        } else if(selectedType === 'status_field') {
            selectContainer.style.display = 'block';
            selectRole.style.display = 'none';
            dateContainer.style.display = 'none';
            sortBy.style.display = 'none';
            sortOrder.style.display = 'none';
            emailContainer.style.display = 'none';
            valueContainer.style.display = 'none';
            operatorSelect.style.display = 'block';
        }else if(selectedType === 'role') {
            selectRole.style.display = 'block';
            dateContainer.style.display = 'none';
            sortBy.style.display = 'none';
            sortOrder.style.display = 'none';
            selectContainer.style.display = 'none';
            emailContainer.style.display = 'none';
            valueContainer.style.display = 'none';
            operatorSelect.style.display = 'none';
        }else if(selectedType === 'created_at' || selectedType === 'updated_at') {
            dateContainer.style.display = 'block';
            sortBy.style.display = 'none';
            sortOrder.style.display = 'none';
            selectRole.style.display = 'none';
            selectContainer.style.display = 'none';
            emailContainer.style.display = 'none';
            valueContainer.style.display = 'none';
            operatorSelect.style.display = 'none';
        }
        else if(selectedType === 'sort_by') {
            sortBy.style.display = 'block';
            sortOrder.style.display = 'none';
            dateContainer.style.display = 'none';
            selectRole.style.display = 'none';
            selectContainer.style.display = 'none';
            emailContainer.style.display = 'none';
            valueContainer.style.display = 'none';
            operatorSelect.style.display = 'none';
        }
        else if(selectedType === 'sort_order') {
            sortOrder.style.display = 'block';
            sortBy.style.display = 'none';
            dateContainer.style.display = 'none';
            selectRole.style.display = 'none';
            selectContainer.style.display = 'none';
            emailContainer.style.display = 'none';
            valueContainer.style.display = 'none';
            operatorSelect.style.display = 'none';
        }

    });
</script>


        <!-- Optional JavaScript; choose one of the two! -->
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
    </body>
    </html>
