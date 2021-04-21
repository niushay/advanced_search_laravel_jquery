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
    <title>Advanced List Search!</title>
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
            margin: 30px;
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

        #filter-button-frame{
            margin-right: 50px;
            direction: rtl;
            margin-top: 20px;
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

    {{--filter button--}}
    <div style="" id="filter-button-frame">
        <div>
            <button class="btn btn-outline-success filter-icon"><i class="fa fa-plus"></i> Add Filter</button>
        </div>
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
        // var data1 = document.getElementById(rowId).querySelectorAll(".field_1");
        var data1 = $("#a_"+rowId)

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
                };                break;
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

{{--change edit inputs--}}
<script>
    let typeee = '';
    let newOptionss = {};

    //When field_1 is changed
    function field1_edit_changed(){

        //get row id
        var rowIdd = event.target.parentNode.parentNode.id;

        //get fields
        // var data11 = document.getElementById(rowIdd).querySelectorAll(".field_1_edit");
        var data11 = $("#a_"+rowIdd)
        var field_1_edit = data11[0].value;
        var field_2_edit = $("#b_"+rowIdd);
        var field_3_edit = $("#c_"+rowIdd);
        field_3_edit.attr('type', 'text');

        //get type of field_1
        var ress = field_1_edit.split(" ");
        typeee = ress[0];

        field_3_edit.show();

        secondOptionss(typeee);
        field_2_edit.empty();
        field_3_edit.val('');
        $.each(newOptionss, function(key,value) {
            field_2_edit.append($("<option></option>")
                .attr("value", value).text(key));
        });

        thirdOptionss(typeee,field_3_edit);
    }

    //set new options for field_2_edit
    function secondOptionss(typeee) {
        switch (typeee) {
            case "int":
            case "bigint":
            case "smallint":
            case "mediumint":
            case "integer":
                newOptionss = {
                    "is":"="
                };
                break;
            case 'varchar':
            case 'char':
            case 'text':
            case 'longtext':
                newOptionss = {
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
                newOptionss = {
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
                newOptionss = {
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
                newOptionss = {
                    'is true':'= 1',
                    'is false':'= 0'
                }
                break;
            case "enum":
                newOptionss = {
                    "is":"=",
                    "is not":"!=",
                    "is set":"is not null",
                    "is not set":"is null"
                }
                break;
            default:
                newOptionss = {
                    'contains':'like',
                    'doesn\'t contain':'not like',
                    'is equal to':'=',
                    'is not equal to':'!=',
                    'is set':'is not null',
                    'is not set':'is null'
                };


        }
    }

    //check if type of field_1_edit is date or boolean or enum make change to the field_3
    function thirdOptionss(typeee, field_3_edit){
        switch (typeee){
            case "tinyint":
                field_3_edit.hide();
                break;
            case "timestamp":
            case 'time':
            case 'datetime':
            case 'date':
                field_3_edit.attr('type', 'date');
                break;
            case "int":
            case "bigint":
            case "smallint":
            case "mediumint":
            case "integer":
                field_3_edit.attr('type', 'number');
                break;
            case "double":
            case "decimal":
            case "float":
                field_3_edit.attr('type', 'number');
                field_3_edit.attr("step","0.01")
                break;
        }
    }

    //when field_2 is changed
    function field2_edit_changed(){
        var rowIdd = event.target.parentNode.parentNode.id;
        var data22 = $("#b_"+rowIdd);
        var field_3_edit = $("#c_"+rowIdd);
        var field_2_edit = data22.children("option:selected").text();
        if(field_2_edit === "is set" || field_2_edit ==="is not set"){
            field_3_edit.hide();
        }else{
            field_3_edit.show();
        }
    }
    ///////////////////////////////
</script>

{{--Load gif when table is unloaded--}}
<script>
    window.addEventListener("load", function () {
        const loader = document.querySelector(".loader");
        loader.className += " hidden"; // class "loader hidden"
    });
</script>

{{--add condition in the main form--}}
<script>
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
</script>

{{--remove condition--}}
<script>
    $(document).on('click', '.btn-remove', function(){
        let rowId = $(this).parent().parent().attr("id");
        $("#"+rowId).remove();
    });
</script>



{{--Edit previous queries and make list of queries--}}
<script>
    var j = 0
    var prv_query = '';
    var res = [];
    var titles = [];
    var types = [];
    var fields_2 = [];
    var fields_3 = [];

    //remove button
    function removeForm(){
        $("#form-frame").addClass("hiddenn");
        $(".dynamically-added").remove();
        $("select").val($("#a_0 option:first").val());
        $("#c_0").val("").attr('type', 'text');
    }

    //check if any form-edit is opened, close it
    function apply(){

        if($(".form-edit").length > 0){
            $(".form-edit").remove()
        }
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


                    /////////show queries in the list

                    let $queries =
                        '<div id="query'+j+'" class="queries_list row" style="position:relative">\n' +
                        '      <p class="each_query m-0 query " id="query' + j + '_p">\n' +
                        '          <a class="btn p-0 edit"> <i class="fa fa-pencil edit_icon"></i></a>'+db+''+
                        '          <a class="btn p-0 remove_query"><i class="remove-query-list-item fa fa-times-circle"></i></a>\n' +
                        '      </p>\n' +
                        '</div>';

                    prv_query = [];
                    $( ".query_list_frame" ).append( $queries );

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

    //Add condition in edit mode
        function addConditionEdit(add_condition_id){

            var split_add_condition_id = add_condition_id.split('_')
            var index = split_add_condition_id[3]

            let last_row_id = $('#form-section-edit_'+index+' .row-edit:last-child').attr('id');

            var split_i = last_row_id.split('_');
            var i = split_i[1];
            i++;
            let html =
                '<div class="row row-edit dynamically-added-edit" style="margin-bottom: 10px" id="'+ index +'_'+i+'">\n' +
                '   <div class="form-group col-3">\n' +
                '       <select class="form-control form-control-sm field_1_edit" name = "rowEdit['+i+'][field_1]" onchange="field1_edit_changed()" id="a_'+index +'_'+i+'">\n' +
                '           <option value="">choose</option>\n' +
                '           @foreach($columns_coll as $column=>$val)\n' +
                '               <option\n' +
                '               value="{{$val->DATA_TYPE}} {{$val->COLUMN_NAME}}">{{$val->COLUMN_NAME}}</option>\n' +
                '           @endforeach\n' +
                '       </select>\n' +
                '    </div>\n' +
                '<div class="form-group col-3">\n' +
                '    <select  class="form-control form-control-sm field_2_edit" name = "rowEdit['+i+'][field_2]" id="b_'+index+'_'+i+'" onchange="field2_edit_changed()">\n' +
                '          <option value="like" selected>contains</option>\n' +
                '          <option value="not like">doesn\'t contain</option>\n' +
                '          <option value="=">is equal to</option>\n' +
                '          <option value="!=">is not equal to</option>\n' +
                '          <option value="is not null">is set</option>\n' +
                '          <option value="is null">is not set</option>\n' +
                '    </select>\n' +
                '    </div>\n' +
                '    <div class="form-group col-3">\n' +
                '        <input class="form-control form-control-sm field_3_edit" name = "rowEdit['+i+'][field_3]" type="text" id="c_'+index+'_'+i+'" >\n' +
                '    </div>\n' +
                '    <div class="form-group col">\n' +
                '        <a class="btn btn-sm btn-danger btn-remove" "><i class="fa fa-minus"></i></a>\n' +
                '    </div>'+
                '</div>';

            $('#form-section-edit_'+index+'').append(html);
    }

    //Append Edit From to the editing element
    $(document).on('click', '.edit', function(e) {
        //close another edit forms
        if($(".form-edit").length > 0){
            $(".form-edit").remove()
        }


        var edited_query_id = $(this).parent().parent().attr('id')  //id of clicked query
        var split_query_id =  edited_query_id.split('');

        var index = split_query_id[5]

        //if the edit form is not already opened, show it
        if ($("form").parents("#" + edited_query_id).length !== 1) {

        //get ID of the row
        let pId = $(this).parent().parent().attr("id");

        //get value of hidden inputs
        var titlesEachRow = $("#titles_" +  pId).val()
        var fields2EachRow = $("#fields2_" +  pId).val()
        var fields3EachRow = $("#fields3_" +  pId).val()
        var field3Split = fields3EachRow.split(",")
        var field2Split = fields2EachRow.split(",")

        //number of the row
        var countRow = titlesEachRow.split(",").length;

        //HTML of form
        var form =
            '<form action="search" method="post" class="from-condition form-edit" style="margin-top: 10px" id="form-edit_' + index + '">\n' +
            '                @csrf\n' +
            '                <div id="form-section-edit_' + index + '" >\n' +
            '                    <div class="row row-edit" style="margin-bottom: 10px;" id="' + index + '_0">\n' +
            '                        <div class="form-group col-3">\n' +
            '                            <select class="form-control form-control-sm field_1_edit" name = "rowEdit[0][field_1]" onchange="field1_edit_changed()" id="a_' + index + '_0">\n' +
            '                                <option value="">choose</option>\n' +
            '                                @foreach($columns_coll as $column=>$val)\n' +
            '                                    <option\n' +
            '                                        value="{{$val->DATA_TYPE}} {{$val->COLUMN_NAME}}">{{$val->COLUMN_NAME}}</option>\n' +
            '                                @endforeach\n' +
            '                            </select>\n' +
            '                        </div>\n' +
            '                        <div class="form-group col-3">\n' +
            '                            <select  class="form-control form-control-sm field_2_edit" name = "rowEdit[0][field_2]" onchange="field2_edit_changed()" id="b_' + index + '_0" >\n' +
            '                                <option value="like" selected>contains</option>\n' +
            '                                <option value="not like">doesn\'t contain</option>\n' +
            '                                <option value="=">is equal to</option>\n' +
            '                                <option value="!=">is not equal to</option>\n' +
            '                                <option value="is not null">is set</option>\n' +
            '                                <option value="is null">is not set</option>\n' +
            '                            </select>\n' +
            '                        </div>\n' +
            '                        <div class="form-group col-3">\n' +
            '                            <input class="form-control form-control-sm field_3_edit" name = "rowEdit[0][field_3]" type="text" id="c_' + index + '_0">\n' +
            '                        </div>\n' +
            '\n' +
            '                    </div>\n' +
            '                </div>\n' +
            '\n' +
            '                <div class="row">\n' +
            '                    {{--add condition button--}}\n' +
            '                    <div class="d-flex justify-content-left col" >\n' +
            '                        <a class="btn" id="add_condition_edit_' + index + '" onclick="addConditionEdit(this.id)" style="font-size:14px"><i class="fa fa-plus" style="margin-right: 5px"></i>Add a condition</a>\n' +
            '                    </div>\n' +
            '                </div>\n' +
            '                <div class="row">\n' +
            '                    {{--Cancel Button--}}\n' +
            '                    <div class="d-flex justify-content-left col" style=" padding-bottom: 10px">\n' +
            '                        <a class="btn btn-outline-danger" onclick="cancelEdit(this.id)" id="' + index + '">cancel\n' +
            '                        </a>\n' +
            '                    </div>\n' +
            '\n' +
            '                    {{--apply button--}}\n' +
            '                    <div class="d-flex justify-content-left col" style=" padding-bottom: 10px">\n' +
            '                        <button id="ajax-submit-edit_' + index + '" type="submit" class="apply btn  btn-success" >save\n' +
            '                        </button>\n' +
            '                    </div>\n' +
            '\n' +
            '                </div>\n' +
            '            </form>';
        $("#" + pId).append(form);

        //hide paragraph
        $("#query" + index + "_p").addClass("hiddenn")

        //Get query selected fields
            var query_types = $("#types_query"+index).val()
            var query_titles = $("#titles_query"+index).val()

            var splitTypes = query_types.split(",")
            var splitTitles = query_titles.split(",")

        //if we have more than one OR condition
        if (countRow > 1) {
            for (var i = 0; i < countRow - 1; i++) {
                addConditionEdit('add_condition_edit_' + index + '')
            }
        }


        var idd_1 = []
        var one = []
        var oneValue = [];
        var split_one = [];
        var typeI = [];
        var newOptionsI = [];
        var two = [];
        var three = [];
        var idd_2 = []

            for (var p = 0; p < countRow; p++) {
                    idd_1[p] = $("#a_" + index+ "_" + p + " option")    //field 1 of row 0 in edit form

                    $(idd_1[p]).each(function () {
                        if ($(this).val() === splitTypes[p] + " " + splitTitles[p])
                            $(this).attr("selected", "selected");
                    });
                    //get type of field_1
                    one[p] = $("#a_" + index + "_" + p);
                    oneValue[p] = one[p].val();

                    split_one[p] = oneValue[p].split(" ");
                    typeI[p] = split_one[p][0]

                    //change input 2 row 0 - edit
                    switch (typeI[p]) {
                        case "int":
                        case "bigint":
                        case "smallint":
                        case "mediumint":
                        case "integer":
                            newOptionsI[p] = {
                                "is": "="
                            };
                            break;
                        case 'varchar':
                        case 'char':
                        case 'text':
                        case 'longtext':
                            newOptionsI[p] = {
                                'contains': 'like',
                                'doesn\'t contain': 'not like',
                                'is equal to': '=',
                                'is not equal to': '!=',
                                'is set': 'is not null',
                                'is not set': 'is null'
                            };
                            break;
                        case 'timestamp':
                        case 'time':
                        case 'datetime':
                        case 'date':
                            newOptionsI[p] = {
                                "is equal to": '=',
                                'is not equal to': '!=',
                                'greater than': '>',
                                'less than': '<',
                                'greater or equal than': '>=',
                                'less or equal than': '<=',
                                'is set': 'is not null',
                                'is not set': 'is null'
                            };
                            break;
                        case "double":
                        case "decimal":
                        case "float":
                            newOptionsI[p] = {
                                "is equal to": '=',
                                'is not equal to': '!=',
                                'greater than': '>',
                                'less than': '<',
                                'greater or equal than': '>=',
                                'less or equal than': '<=',
                                'is set': 'is not null',
                                'is not set': 'is null'
                            };
                            break;
                        case "tinyint":
                            newOptionsI[p] = {
                                'is true': '= 1',
                                'is false': '= 0'
                            }
                            break;
                        case "enum":
                            newOptionsI[p] = {
                                "is": "=",
                                "is not": "!=",
                                "is set": "is not null",
                                "is not set": "is null"
                            }
                            break;
                        default:
                            newOptionsI[p] = {
                                'contains': 'like',
                                'doesn\'t contain': 'not like',
                                'is equal to': '=',
                                'is not equal to': '!=',
                                'is set': 'is not null',
                                'is not set': 'is null'
                            };
                    }

                    two[p] = $("#b_" + index + "_" + p)
                    two[p].empty();
                    $.each(newOptionsI[p], function (key, value) {
                        two[p].append($("<option></option>")
                            .attr("value", value).text(key));
                    });

                    idd_2[p] = $("#b_" + index+ "_" + p + " option")
                    $(idd_2[p]).each(function () {
                        if ($(this).val() === field2Split[p])
                            $(this).attr("selected", "selected");
                    });

                    // change input 3 row 0 - edit
                    three[p] = $("#c_" + index + "_" + p)
                    switch (three[p]) {
                        case "tinyint":
                            three[p].hide();
                            break;
                        case "timestamp":
                        case 'time':
                        case 'datetime':
                        case 'date':
                            three[p].attr('type', 'date');
                            break;
                        case "int":
                        case "bigint":
                        case "smallint":
                        case "mediumint":
                        case "integer":
                            three[p].attr('type', 'number');
                            break;
                        case "double":
                        case "decimal":
                        case "float":
                            three[p].attr('type', 'number');
                            three[p].attr("step", "0.01")
                            break;
                    }

                    //set the third input value
                    three[p].val(field3Split[p])

                    //change input 3 regarded to input2 changing
                    if(field2Split[p] === "is not null" || field2Split[p]  === "is null"){
                        three[p].hide();
                    }else{
                        three[p].show();
                    }

            }

            ////////save button
            $(document).ready(function (){
                $('#ajax-submit-edit_' + index + '').click(function (e) {
                    e.preventDefault();
                    $.ajaxSetup({
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        }
                    });

                    $.ajax({
                        url: "{{url('/edit')}}",
                        method: 'POST',
                        data: {
                            row_count: $('.row-edit').length,
                            data: $('.form-edit input:visible, .form-edit select:visible').serializeArray(),
                        },
                        success: function (result) {
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


                            /////////show queries in the list

                            let $queries =
                                '<div id="query' + j + '" class="queries_list row" style="position:relative">\n' +
                                '      <p class="each_query m-0 query" id="query' + j + '_p">\n' +
                                '          <a class="btn p-0 edit"> <i class="fa fa-pencil edit_icon"></i></a>' + db + '' +
                                '          <a class="btn p-0 remove_query"><i class="remove-query-list-item fa fa-times-circle"></i></a>\n' +
                                '      </p>\n' +
                                '</div>';
                            $( ".query_list_frame" ).append( $queries );
                            $("#"+pId).remove()

                            //set hidden inputs
                            //get titles array and types array
                            var titless = result.titles;
                            var typess = result.types;
                            var fieldss_2 = result.fields_2;
                            var fieldss_3 = result.fields_3;

                            //append types hidden input
                            $('<input>').attr({
                                type: 'hidden',
                                value: typess,
                                id:'types_query'+j,
                            }).appendTo('#query'+j);

                            //append titles hidden input
                            $('<input>').attr({
                                type: 'hidden',
                                value: titless,
                                id:'titles_query'+j,
                            }).appendTo('#query'+j);

                            //append fields_2 hidden input
                            $('<input>').attr({
                                type: 'hidden',
                                value: fieldss_2,
                                id:'fields2_query'+j,
                            }).appendTo('#query'+j);

                            //append fields_3 hidden input
                            $('<input>').attr({
                                type: 'hidden',
                                value: fieldss_3,
                                id:'fields3_query'+j,
                            }).appendTo('#query'+j);

                            j++;
                        }
                    })
                })
            })
    }
        else{
            $("#form-edit_"+index).remove();
        }
    });

    //show form when click the filter button
    $(document).ready(function (){
            $(".filter-icon").click(function () {
                $("#form-frame").removeClass("hiddenn");
            });
    });

    //Cancel edit
    function cancelEdit(cancelId){
        $("#query" + cancelId + "_p").removeClass("hiddenn")
        $("#form-edit_"+cancelId).remove();
    }

</script>




<!-- Option 1: Bootstrap Bundle with Popper -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta1/dist/js/bootstrap.bundle.min.js" integrity="sha384-ygbV9kiqUc6oa4msXn9868pTtWMgiQaeYH7/t7LECLbyPA2x65Kgf80OJFdroafW" crossorigin="anonymous"></script>

</body>
</html>
