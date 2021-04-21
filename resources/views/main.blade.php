<!doctype html>
<html lang="en">
<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-giJF6kkoqNQ00vy+HMDP7azOuL0xtbfIcaT9wjKHr8RbDVddVHyTfAAsrekwKmP1" crossorigin="anonymous">

    {{--Font AweSome--}}
    <script src="https://use.fontawesome.com/ca06c71769.js"></script>

    {{--Title--}}
    <title>Advanced Search!</title>
    {{--style css--}}
    <style>
        html, body{
            padding:20px 50px
        }
        .loader {
            position: fixed;
            z-index: 99;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: white;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .loader > img {
            width: 200px;
        }

        .loader.hidden {
            animation: fadeOut 1s;
            animation-fill-mode: forwards;
        }

        @keyframes fadeOut {
            100% {
                opacity: 0;
                visibility: hidden;
            }
        }

        .sort-down-icon{
            color: #78909C;
            padding-left: 3px;
            position: relative;
            top: 0;
        }

        .close{
            color: #78909C;
            position: relative;
            top:3px
        }
        #form-frame{
            background:white;
            height: auto;
            border-radius:10px;
            box-shadow: 1px 1px 1px 1px #CFD8DC;
            position: absolute;
            /*top: 80px;*/
            right: 6%;
            margin: 25px;
            width:auto;
            /*border: 1px solid #607D8B;*/
            padding-top: 5px;
            padding-right: 0;
            padding-left: 10px;
        }


        /*.from-condition{*/
        /*    width: auto;*/
        /*}*/
        .hiddenn{
            display: none;
        }
        #add-condition{
            font-size: 15px
        }

        .parent_frame{
            background: #FAFAFA;
            border: 1px solid #607D8B;
            margin-top: 25px;
            margin-left: 67%;
            width: 31%;
            border-radius: 25px;
            padding: 1px 10px;
            /*position: absolute;*/
        }

        .search{
            width: 0;
        }

        .queries{
            background: #ECEFF1;
            display: inline-block;
            border: 1px solid #90A4AE;
            border-radius: 15px;
            padding: 0 3px
        }

        .query_list_frame{
            margin-top: 15px;
            margin-left: 69%;
            margin-right: 40px;
        }
        .queries_list{
            background: #ECEFF1;
            display: block;
            border: 1px solid #90A4AE;
            border-radius: 5px;
            padding: 0 3px;
            margin-top: 5px;
            width: 408px
        }
        .each_query{
            width : auto;
        }
        .remove-query-list-item{
            color: #546E7A;
            position: absolute;
            right:2px;
            padding:0 0 0 2px;
            top:6px;
            margin-right:2px
        }
        .edit{
            color: #546E7A;
        }
        .edit_icon{
            padding-right: 10px;
        }

    </style>

    {{--JQuery--}}
    <script
        src="https://code.jquery.com/jquery-3.5.1.min.js"
        integrity="sha256-9/aliU8dGd2tb6OSsuzixeV4y/faTqgFtohetphbbj0="
        crossorigin="anonymous">
    </script>

</head>
<body>
{{--queries--}}
<div>
    {{--list of queries--}}
    <div class="query_list_frame">

    </div>

    {{--search bar--}}
    <div class="parent_frame row">
        <button class="col p-0 btn  sort-down-icon"><i class="fa fa-sort-down"></i></button>
        <div id="prv-query" class="col-md-10 p-0" style=" font-size: 12px;">
        </div>
        <button class="col p-0 btn remove_all"><i class="fa fa-times-circle close"></i></button>
    </div>

    {{--search form--}}
    <div id="form-frame"  class="hiddenn">
        <form action="{{route('search')}}" method="post" class="from-condition">
            @csrf
            <div id="form-section">
                <div class="row">
                    <a class="btn col-md-1" >
                        <i onclick="removeForm()" class="fa fa-times-circle" style="position: absolute; top: 6px; right: 10px; color: #BDBDBD"></i>
                    </a>
                </div>
                <div class="row" style="margin-bottom: 10px" id="0">
                    <div class="form-group col-3">
                        <select class="form-control form-control-sm field_1" name = "rows[0][field_1]" onchange="field1_changed()" id="a_0">
                            <option value="">choose</option>
                            @foreach($columns_coll as $column=>$val)
                                <option
                                    value="{{$val->DATA_TYPE}} {{$val->COLUMN_NAME}}">{{$val->COLUMN_NAME}}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group col-3">
                        <select  class="form-control form-control-sm field_2" name = "rows[0][field_2]" onchange="field2_changed()" id="b_0" >
                            <option value="like" selected>contains</option>
                            <option value="not like">doesn't contain</option>
                            <option value="=">is equal to</option>
                            <option value="!=">is not equal to</option>
                            <option value="is not null">is set</option>
                            <option value="is null">is not set</option>
                        </select>
                    </div>
                    <div class="form-group col-3">
                        <input class="form-control form-control-sm field_3" name = "rows[0][field_3]" type="text" id="c_0">
                    </div>

                </div>
            </div>

            <div class="row">
                {{--add condition button--}}
                <div class="d-flex justify-content-center col" style="margin-top: 10px;">
                    <a id="add-condition" class="btn" onclick="addCondition()"><i class="fa fa-plus" style="padding-right: 10px"></i>Add a condition</a>
                </div>
                {{--apply button--}}
                <div class="d-flex justify-content-center col" style="margin-top: 10px; padding-bottom: 10px">
                    <button id="ajax-submit" type="submit" class="apply btn btn-sm btn-secondary" onclick="apply()">Apply
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>




{{--table--}}
<div class="d-flex justify-content-center" id="table_parent" style="margin-top: 50px">
    <div class="col-md-9">
        <table class="table table-striped table-hover table-bordered" id="records_table">
            {{--loader gif--}}
            <div class="loader" >
                <img src="loader.gif" alt="Loading..." />
            </div>

            <thead>
            <tr>
                <th scope="col">id</th>
                <th scope="col">name</th>
                <th scope="col">address</th>
                <th scope="col">age</th>
            </tr>
            </thead>
            <tbody>

            </tbody>
        </table>
        <div id="pagination">
            {{--                    {{$people->links()}}--}}
        </div>
    </div>
</div>

{{--change search inputs--}}
<script>

    let type = '';
    let newOptions = {};

    //When field_1 is changed
    function field1_changed(){

        //get row id
        var rowId = event.target.parentNode.parentNode.id;

        //get fields
        var data1 = document.getElementById(rowId).querySelectorAll(".field_1");
        var field_1 = data1[0].value;
        var field_2 = $("#b_"+rowId);
        var field_3 = $("#c_"+rowId);
        field_3.attr('type', 'text');

        //get type of field_1
        var res = field_1.split(" ");
        type = res[0];

        field_3.show();

        secondOptions(type);
        field_2.empty();
        field_3.val('');
        $.each(newOptions, function(key,value) {
            field_2.append($("<option></option>")
                .attr("value", value).text(key));
        });

        thirdOptions(type,field_3);
    }

    //set new options for field_2
    function secondOptions(type) {
        switch (type) {
            case "int":
            case "bigint":
            case "smallint":
            case "mediumint":
            case "integer":
                newOptions = {
                    "is":"="
                };
                break;
            case 'varchar':
            case 'char':
            case 'text':
            case 'longtext':
                newOptions = {
                    'contains':'like',
                    'doesn\'t contain':'not like',
                    'is equal to':'=',
                    'is not equal to':'!=',
                    'is set':'is not null',
                    'is not set':'is null'
                };
                break;
            case 'timestamp':
            case 'time':
            case 'datetime':
            case 'date':
                newOptions = {
                    "is equal to":'=',
                    'is not equal to':'!=',
                    'greater than':'>',
                    'less than':'<',
                    'greater or equal than':'>=',
                    'less or equal than':'<=',
                    'is set':'is not null',
                    'is not set':'is null'
                };
                break;
            case "double":
            case "decimal":
            case "float":
                newOptions = {
                    "is equal to":'=',
                    'is not equal to':'!=',
                    'greater than':'>',
                    'less than':'<',
                    'greater or equal than':'>=',
                    'less or equal than':'<=',
                    'is set':'is not null',
                    'is not set':'is null'
                };
                break;
            case "tinyint":
                newOptions = {
                    'is true':'= 1',
                    'is false':'= 0'
                }
                break;
            case "enum":
                newOptions = {
                    "is":"=",
                    "is not":"!=",
                    "is set":"is not null",
                    "is not set":"is null"
                }
                break;
            default:
                newOptions = {
                    'contains':'like',
                    'doesn\'t contain':'not like',
                    'is equal to':'=',
                    'is not equal to':'!=',
                    'is set':'is not null',
                    'is not set':'is null'
                };


        }
    }

    //check if type of field_1 is date or boolean or enum make change to the field_3
    function thirdOptions(type, field_3){
        switch (type){
            case "tinyint":
                field_3.hide();
                break;
            case "timestamp":
            case 'time':
            case 'datetime':
            case 'date':
                field_3.attr('type', 'date');
                break;
            case "int":
            case "bigint":
            case "smallint":
            case "mediumint":
            case "integer":
                field_3.attr('type', 'number');
                break;
            case "double":
            case "decimal":
            case "float":
                field_3.attr('type', 'number');
                field_3.attr("step","0.01")
                break;
        }
    }

    //when field_2 is changed
    function field2_changed(){
        var rowId = event.target.parentNode.parentNode.id;
        var data2 = $("#b_"+rowId);
        var field_3 = $("#c_"+rowId);
        var field_2 = data2.children("option:selected").text();
        if(field_2 === "is set" || field_2 ==="is not set"){
            field_3.hide();
        }else{
            field_3.show();
        }
    }

</script>

{{--hide form--}}
<script>
    function removeForm(){
        $("#form-frame").addClass("hiddenn");
        $(".dynamically-added").remove();
        $("select").val($("#a_0 option:first").val());
        $("#c_0").val("").attr('type', 'text');
    }
</script>

{{--Search Ajax--}}
<script>
    var j = 0
    var prv_query = '';
    var res = [];
    var titles = [];
    var types = [];
    var fields_2 = [];
    var fields_3 = [];

    function apply(){

        //hide form
        $("#form-frame").addClass("hiddenn");
        //get previous queries
        if($('.query').text()!==""){
            $('.query').each(function(){
                prv_query += $(this).text() + "<br>";
            })
            res = prv_query.split("<br>");
        }else{
            res = null;
        }
    }

    //remove_queries
    $(document).on('click', '.remove_query', function(e){
        let pId = $(this).parent().parent().attr("id");
        $("#"+pId).remove();

        if($('.query').text()!==""){
            $('.query').each(function(){
                prv_query += $(this).text() + "<br>";
            })
            res = prv_query.split("<br>");
        }else{
            res = null;
        }
        e.preventDefault();
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        $.ajax({
            url:"{{url('/search')}}",
            method:'POST',
            data:{
                prv_query : res,
            },
            success: function(people){
                $("#records_table tbody tr").remove();
                var trHTML = '';
                $.each(people.result, function (i, item) {
                    trHTML += '<tr><td>' +
                        item['id'] + '</td><td>' +
                        item['name'] + '</td><td>' +
                        item['address'] + '</td><td>' +
                        item['age'] + '</td></tr>';
                });
                $('#records_table').append(trHTML);

            }
        })
    });

    //remove all queries
    $(document).on('click', '.remove_all', function(e){

        $(".queries").remove();
        e.preventDefault();
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        $.ajax({
            url:"{{url('/search')}}",
            method:'POST',
            data:{
                prv_query : 'all',
            },
            success: function(people){
                $("#records_table tbody tr").remove();
                var trHTML = '';
                $.each(people.result, function (i, item) {
                    trHTML += '<tr><td>' +
                        item['id'] + '</td><td>' +
                        item['name'] + '</td><td>' +
                        item['address'] + '</td><td>' +
                        item['age'] + '</td></tr>';
                });
                $('#records_table').append(trHTML);

            }
        })
    });


    $(document).ready(function (){
        //search data
        $('#ajax-submit').click(function (e) {
            e.preventDefault();
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            $.ajax({
                url:"{{url('/search')}}",
                method:'POST',
                data:{
                    row_count : $('.row').length,
                    prv_query : res,
                    data : $('.from-condition input, .from-condition select, .prv-query p').serializeArray(),
                },
                success: function(result){
                    $("#records_table tbody tr").remove();
                    var trHTML = '';
                    $.each(result.result, function (i, item) {
                        trHTML += '<tr><td>' +
                            item['id'] + '</td><td>' +
                            item['name'] + '</td><td>' +
                            item['address'] + '</td><td>' +
                            item['age'] + '</td></tr>';
                    });
                    $('#records_table').append(trHTML);

                    var db = result.db;


                    /////////show queries in search bar
                    let $queries =
                        '<div class="queries" id="query'+j+'" >\n' +
                        '     <p class="m-0 query">'+db+'<a class="btn p-0 remove_query" ><i  style="padding: 0; color: #636b6f" class="fa fa-times-circle"></i></a></p>\n' +
                        '</div>';
                    prv_query = [];
                    $( "#prv-query" ).append( $queries );
                    j++;

                    //get titles array and types array
                    titles = result.titles;
                    types = result.types;
                    fields_2 = result.fields_2;
                    fields_3 = result.fields_3;

                    //append types hidden input
                    $('<input>').attr({
                        type: 'hidden',
                        value: types,
                        id:'types_query'+j,
                    }).appendTo('#query'+j);

                    //append titles hidden input
                    $('<input>').attr({
                        type: 'hidden',
                        value: titles,
                        id:'titles_query'+j,
                    }).appendTo('#query'+j);

                    //append fields_2 hidden input
                    $('<input>').attr({
                        type: 'hidden',
                        value: fields_2,
                        id:'fields2_query'+j,
                    }).appendTo('#query'+j);

                    //append fields_3 hidden input
                    $('<input>').attr({
                        type: 'hidden',
                        value: fields_3,
                        id:'fields3_query'+j,
                    }).appendTo('#query'+j);


                    j++;

                    //empty form after each request
                    $(".dynamically-added").remove();
                    $("select").val($("#a_0 option:first").val());
                    $("#c_0").val("").attr('type', 'text');
                }
            })
        })

        //get total data
        fetch_customer_data();

        //show form when click the sortdown icon
        $(".sort-down-icon").click(function(){
            $("#form-frame").removeClass("hiddenn");
        });
    });
    function fetch_customer_data() {
        $.ajax({
            url:"{{ route('totaldata') }}",
            method:'get',
            dataType:'json',
            success:function(data)
            {
                var trHTML = '';
                $.each(data.data, function (i, item) {
                    trHTML += '<tr><td>' +
                        item['id'] + '</td><td>' +
                        item['name'] + '</td><td>' +
                        item['address'] + '</td><td>' +
                        item['age'] + '</td></tr>';
                });
                $('#records_table').append(trHTML);
            }
        })
    }


</script>

{{--Load gif when table is unloaded--}}
<script>
    window.addEventListener("load", function () {
        const loader = document.querySelector(".loader");
        loader.className += " hidden"; // class "loader hidden"
    });
</script>

{{--Add condition row--}}
<script>
    //add condition in the main form
    function addCondition(){
        let i = $('#form-section .row:last-child').attr('id');
        i++;
        let html =
            '<div class="row dynamically-added" style="margin-bottom: 10px" id="'+i+'">\n' +
            '   <div class="form-group col-3">\n' +
            '       <select class="form-control form-control-sm field_1" name = "rows['+i+'][field_1]" onchange="field1_changed()" id="a_'+i+'">\n' +
            '           <option value="">choose</option>\n' +
            '           @foreach($columns_coll as $column=>$val)\n' +
            '               <option\n' +
            '               value="{{$val->DATA_TYPE}} {{$val->COLUMN_NAME}}">{{$val->COLUMN_NAME}}</option>\n' +
            '           @endforeach\n' +
            '       </select>\n' +
            '    </div>\n' +
            '<div class="form-group col-3">\n' +
            '    <select  class="form-control form-control-sm field_2" name = "rows['+i+'][field_2]" id="b_'+i+'" onchange="field2_changed()">\n' +
            '          <option value="like" selected>contains</option>\n' +
            '          <option value="not like">doesn\'t contain</option>\n' +
            '          <option value="=">is equal to</option>\n' +
            '          <option value="!=">is not equal to</option>\n' +
            '          <option value="is not null">is set</option>\n' +
            '          <option value="is null">is not set</option>\n' +
            '    </select>\n' +
            '    </div>\n' +
            '    <div class="form-group col-3">\n' +
            '        <input class="form-control form-control-sm field_3" name = "rows['+i+'][field_3]" type="text" id="c_'+i+'" >\n' +
            '    </div>\n' +
            '    <div class="form-group col">\n' +
            '        <a class="btn btn-sm btn-danger btn-remove" "><i class="fa fa-minus"></i></a>\n' +
            '    </div>'+
            '</div>';

        $('#form-section').append(html);
    }

    //add condition in edit
    function addConditionEdit(){

        let i = $('#form-section-edit .row:last-child').attr('id');
        i++;
        let html =
            '<div class="row dynamically-added" style="margin-bottom: 10px" id="'+i+'">\n' +
            '   <div class="form-group col-3">\n' +
            '       <select class="form-control field_1" name = "rowEdit['+i+'][field_1]" onchange="field1_changed()" id="a_'+i+'">\n' +
            '           <option value="">choose</option>\n' +
            '           @foreach($columns_coll as $column=>$val)\n' +
            '               <option\n' +
            '               value="{{$val->DATA_TYPE}} {{$val->COLUMN_NAME}}">{{$val->COLUMN_NAME}}</option>\n' +
            '           @endforeach\n' +
            '       </select>\n' +
            '    </div>\n' +
            '<div class="form-group col-3">\n' +
            '    <select  class="form-control field_2" name = "rowEdit['+i+'][field_2]" id="b_'+i+'" onchange="field2_changed()">\n' +
            '          <option value="like" selected>contains</option>\n' +
            '          <option value="not like">doesn\'t contain</option>\n' +
            '          <option value="=">is equal to</option>\n' +
            '          <option value="!=">is not equal to</option>\n' +
            '          <option value="is not null">is set</option>\n' +
            '          <option value="is null">is not set</option>\n' +
            '    </select>\n' +
            '    </div>\n' +
            '    <div class="form-group col-3">\n' +
            '        <input class="form-control field_3" name = "rowEdit['+i+'][field_3]" type="text" id="c_'+i+'" >\n' +
            '    </div>\n' +
            '    <div class="form-group col">\n' +
            '        <a class="btn btn-sm btn-danger btn-remove" "><i class="fa fa-minus"></i></a>\n' +
            '    </div>'+
            '</div>';

        $('#form-section-edit').append(html);
    }
</script>

{{--remove condition--}}
<script>
    $(document).on('click', '.btn-remove', function(){
        let rowId = $(this).parent().parent().attr("id");
        $("#"+rowId).remove();
    });
</script>


<!-- Option 1: Bootstrap Bundle with Popper -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta1/dist/js/bootstrap.bundle.min.js" integrity="sha384-ygbV9kiqUc6oa4msXn9868pTtWMgiQaeYH7/t7LECLbyPA2x65Kgf80OJFdroafW" crossorigin="anonymous"></script>

</body>
</html>
