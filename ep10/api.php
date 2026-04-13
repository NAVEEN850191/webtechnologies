<?php
header('Content-Type: application/json');

$students = [
    ["id"=>1,"name"=>"naveen","age"=>20,"marks"=>78,"course"=>"IT"],
    ["id"=>2,"name"=>"dravid","age"=>21,"marks"=>85,"course"=>"CS"],
    ["id"=>3,"name"=>"navya","age"=>22,"marks"=>88,"course"=>"AIDS"],
    ["id"=>4,"name"=>"Ananya","age"=>19,"marks"=>92,"course"=>"AI"],
    ["id"=>5,"name"=>"Vikram","age"=>22,"marks"=>74,"course"=>"IT"],
    ["id"=>6,"name"=>"Priya","age"=>20,"marks"=>88,"course"=>"DS"],
    ["id"=>7,"name"=>"Hemanth","age"=>21,"marks"=>76,"course"=>"AIDS"],
    ["id"=>8,"name"=>"Karun","age"=>21,"marks"=>69,"course"=>"IT"],
    ["id"=>9,"name"=>"Sneha","age"=>19,"marks"=>95,"course"=>"AI"],
    ["id"=>10,"name"=>"Rohit","age"=>20,"marks"=>78,"course"=>"AIDS"],
    ["id"=>11,"name"=>"Arjun","age"=>20,"marks"=>81,"course"=>"CS"],
    ["id"=>12,"name"=>"Meera","age"=>22,"marks"=>86,"course"=>"DS"],
    ["id"=>13,"name"=>"Bhavana","age"=>20,"marks"=>86,"course"=>"AIDS"],
    ["id"=>14,"name"=>"Rohan","age"=>21,"marks"=>72,"course"=>"IT"],
    ["id"=>15,"name"=>"Ali","age"=>22,"marks"=>71,"course"=>"SE"],
    ["id"=>16,"name"=>"sravani","age"=>19,"marks"=>89,"course"=>"ECE"],
    ["id"=>17,"name"=>"sirish","age"=>21,"marks"=>77,"course"=>"SE"],
    ["id"=>18,"name"=>"yash","age"=>20,"marks"=>84,"course"=>"ME"],
    ["id"=>19,"name"=>"manoj","age"=>22,"marks"=>80,"course"=>"ECE"],
    ["id"=>20,"name"=>"ramya","age"=>19,"marks"=>91,"course"=>"me"]  
];

$search = isset($_GET['search']) ? strtolower($_GET['search']) : '';
$course = isset($_GET['course']) ? $_GET['course'] : 'All';
$sortDesc = isset($_GET['sortDesc']) ? filter_var($_GET['sortDesc'], FILTER_VALIDATE_BOOLEAN) : false;

$filtered = array_filter($students, function($s) use ($search, $course) {
    if ($search !== "" && strpos(strtolower($s['name']), $search) === false) {
        return false;
    }
    if ($course !== "All" && $s['course'] !== $course) {
        return false;
    }
    return true;
});

$filtered = array_values($filtered);

if (isset($_GET['sortDesc'])) {
    usort($filtered, function($a, $b) use ($sortDesc) {
        if ($sortDesc) {
            return $b['marks'] <=> $a['marks'];
        } else {
            return $a['marks'] <=> $b['marks'];
        }
    });
}

echo json_encode($filtered);
?>