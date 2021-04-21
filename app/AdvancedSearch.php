<?php


namespace App;




use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\DB;

class AdvancedSearch
{
    public function switch_db($field_1_title, $field_2, $field_3)
    {
        switch ($field_2) {
            case 'like':
            case 'not like':
                return ' or ' . $field_1_title .' '. $field_2 ." '%" . $field_3 . "%'";
                break;
            case 'is not null':
            case 'is null':
            case '= 1':
            case '= 0':
                return ' or ' . $field_1_title . ' ' . $field_2;
                break;
            default:
                return ' or ' .$field_1_title . $field_2 . "'" . $field_3 . "'";
                break;
        }
    }

//    public function doSearch(Request $request, $table_name)
//    {
//        $conn = mysqli_connect("localhost", "root", "", "search");
//        $with_any_one_of = "";
//        $with_the_exact_of = "";
//        $without = "";
//        $starts_with = "";
//        $search_in = "";
//        $advance_search_submit = "";
//
//        $queryCondition = "";
//        if(!empty($_POST["search"])) {
//            $advance_search_submit = $_POST["advance_search_submit"];
//            foreach($_POST["search"] as $k=>$v){
//                if(!empty($v)) {
//
//                    $queryCases = array("with_any_one_of","with_the_exact_of","without","starts_with");
//                    if(in_array($k,$queryCases)) {
//                        if(!empty($queryCondition)) {
//                            $queryCondition .= " AND ";
//                        } else {
//                            $queryCondition .= " where ";
//                        }
//                    }
//                    switch($k) {
//                        case "with_any_one_of":
//                            $with_any_one_of = $v;
//                            $wordsAry = explode(" ", $v);
//                            $wordsCount = count($wordsAry);
//                            for($i=0;$i<$wordsCount;$i++) {
//                                if(!empty($_POST["search"]["search_in"])) {
//                                    $queryCondition .= $_POST["search"]["search_in"] . " LIKE '%" . $wordsAry[$i] . "%'";
//                                } else {
//                                    $queryCondition .= "title LIKE '" . $wordsAry[$i] . "%' OR description LIKE '" . $wordsAry[$i] . "%'";
//                                }
//                                if($i!=$wordsCount-1) {
//                                    $queryCondition .= " OR ";
//                                }
//                            }
//                            break;
//                        case "with_the_exact_of":
//                            $with_the_exact_of = $v;
//                            if(!empty($_POST["search"]["search_in"])) {
//                                $queryCondition .= $_POST["search"]["search_in"] . " LIKE '%" . $v . "%'";
//                            } else {
//                                $queryCondition .= "title LIKE '%" . $v . "%' OR description LIKE '%" . $v . "%'";
//                            }
//                            break;
//                        case "without":
//                            $without = $v;
//                            if(!empty($_POST["search"]["search_in"])) {
//                                $queryCondition .= $_POST["search"]["search_in"] . " NOT LIKE '%" . $v . "%'";
//                            } else {
//                                $queryCondition .= "title NOT LIKE '%" . $v . "%' AND description NOT LIKE '%" . $v . "%'";
//                            }
//                            break;
//                        case "starts_with":
//                            $starts_with = $v;
//                            if(!empty($_POST["search"]["search_in"])) {
//                                $queryCondition .= $_POST["search"]["search_in"] . " LIKE '" . $v . "%'";
//                            } else {
//                                $queryCondition .= "title LIKE '" . $v . "%' OR description LIKE '" . $v . "%'";
//                            }
//                            break;
//                        case "search_in":
//                            $search_in = $_POST["search"]["search_in"];
//                            break;
//                    }
//                }
//            }
//        }
//        $orderby = " ORDER BY id desc";
//        $sql = DB::select('select * from'.$table_name . $queryCondition);
//        $result = mysqli_query($conn,$sql);
//
//    }
}
