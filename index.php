<?php
session_start();
require_once 'db.php';
require_once 'employee.php';

if(isset($_POST['submit']))
{
    // Get input by post method and filter them before inserting into database
    $name = filter_input(INPUT_POST, 'name' , FILTER_SANITIZE_STRING);
    $address = filter_input(INPUT_POST, 'address' , FILTER_SANITIZE_STRING);
    $age = filter_input(INPUT_POST, 'age' , FILTER_SANITIZE_NUMBER_INT);
    $salary = filter_input(INPUT_POST, 'salary' , FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
    $tax = filter_input(INPUT_POST, 'tax' , FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
    //$employee = new Employee($name, $age, $address, $tax, $salary);
    
    $params =   array(
            ':name'     => $name,
            ':age'      => $age,
            ':address'  => $address,
            ':salary'   => $salary,
            ':tax'      => $tax
    );
    // Inserting or updating data the employee
    if (isset($_GET['action']) && $_GET['action'] == 'edit' && isset($_GET['id'])) 
    {
        $id = filter_input(INPUT_GET, 'id' , FILTER_SANITIZE_NUMBER_INT);
        $sql = 'UPDATE `employees` SET `name` = :name, `address`= :address, `age`= :age, `tax` = :tax , `salary` =:salary WHERE `id`= :id';
        $params[':id'] = $id;
    }else
    {
        $sql = 'INSERT INTO `employees` SET `name` = :name, `address`= :address, `age`= :age, `tax` = :tax , `salary` =:salary';
    }
    
    $stmt = $connection->prepare($sql);
    if($stmt->execute($params) == true
    )
    {
        $_SESSION['message'] = 'Employee, '. $name .' has been succefully saved';
        header('Location: index.php');
        session_write_close();
        exit;
    }
    else{
        $error = true;
        $_SESSION['message'] ='Error saving employee, '.$name;
    }
}

//
if (isset($_GET['action']) && $_GET['action'] == 'edit' && isset($_GET['id'])) 
{
    $id = filter_input(INPUT_GET, 'id' , FILTER_SANITIZE_NUMBER_INT);
    if ($id > 0) 
    {
        $sql = 'SELECT * FROM `employees` WHERE `id` = :id';
        $result = $connection->prepare($sql);
        $foundUser = $result->execute(array(':id'=> $id));
        if ($foundUser == true) 
        {
            $user = $result->fetchAll(PDO::FETCH_CLASS | PDO::FETCH_PROPS_LATE, 'Employee', array('name','age','address','salary','tax' ));
            $user = array_shift($user);
        }
    }
}

if (isset($_GET['action']) && $_GET['action'] == 'delete' && isset($_GET['id'])) 
{
    $id = filter_input(INPUT_GET, 'id' , FILTER_SANITIZE_NUMBER_INT);
    if ($id > 0) 
    {
        $sql = 'DELETE FROM `employees` WHERE `id` = :id';
        $result = $connection->prepare($sql);
        $foundUser = $result->execute(array(':id'=> $id));
        if ($foundUser == true) 
        {
            $_SESSION['message'] = 'Employee deleted succefully';
            header('Location: index.php');
            session_write_close();
            exit;
        }
    }
}

    // Reading the data back from database
    $sql = 'SELECT * FROM `employees`';
    $stmt = $connection->query($sql);
    $result = $stmt->fetchAll(PDO::FETCH_CLASS | PDO::FETCH_PROPS_LATE, 'Employee', array('name','age','address','salary','tax' ));
    $result = (is_array($result) && !empty($result)) ? $result : false;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PDO</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.13.0/css/all.min.css">
</head>
<body>

    <div class="wrapper"> <!-- div wrapper start  -->
        <div class="empform"><!-- div form start  -->
            <form class="appform" method="post" enctype="application/x-www-form-urlencoded">
            <fieldset>
                <legend>Employee Informations</legend>
                <?php if(isset($_SESSION['message'])) { ?>
                <p class="message <?= isset($error) ? 'error': ''; ?>"> 
                    <a id="home" href="index.php"></a>
                    <?= $_SESSION['message'] ?>    
                </p>
                <?php
                unset($_SESSION['message']);
                    }
                ?>   
                <table>
                    <tr>
                        <td>
                            <label for="name">Employee Name: </label>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <input type="text" value="<?= isset($user) ? $user->name : ''; ?>" id="name" name="name" placeholder="Write employee name here" maxlength="50">
                        </td>
                    </tr>
                     <tr>
                        <td>
                            <label for="age">Employee Age: </label>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <input type="number" value="<?= isset($user) ? $user->age : ''; ?>" name="age" id="age" min="22" max="60">
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <label for="address">Employee Address: </label>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <input type="text" value="<?= isset($user) ? $user->address : ''; ?>" name="address" id="address" placeholder="Write employee address here" maxlength="100">
                        </td>
                    </tr>
                     <tr>
                        <td>
                            <label for="salary">Employee Salary: </label>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <input type="number" value="<?= isset($user) ? $user->salary : ''; ?>" name="salary" step="0.01" id="salary" min="1500" max="9000">
                        </td>
                    </tr>
                     <tr>
                        <td>
                            <label for="tax">Employee Tax: </label>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <input type="number" value="<?= isset($user) ? $user->tax : ''; ?>" name="tax" step="0.01" id="tax" min="1" max="5">
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <input type="submit" name="submit" value="Save">
                        </td>
                    </tr>
                </table>
            </fieldset>
            </form>
        </div><!-- div form end  -->

        <div class="employees"> <!-- div employees start  -->
			<table>
				<thead>
					<tr>
						<th>Name</th>
						<th>Age</th>
						<th>Address</th>
						<th>Salary</th>
                        <th>Tax (%)</th>
						<th>Control</th>
					</tr>
				</thead>
				<tbody>
                    <?php if(false !== $result){ 
                        foreach($result as $employee){
                  ?>
					<tr>
                        <td><?= $employee->name ?></td>
                        <td><?= $employee->age ?></td>
                        <td><?= $employee->address ?></td>
                        <td><?= round($employee->calcSalary()) ?> LE</td>
                        <td><?= $employee->tax ?></td>
						<td>
                            <a href="index.php?action=edit&id=<?= $employee->id ?>">
                                <i class="fa fa-edit"></i>
                            </a>
                            <a  href="index.php?action=delete&id=<?= $employee->id ?>" onclick="if(!confirm('Do you want delete this user?')) return false;">
                                <i class="fa fa-times"></i>
                            </a>
                        </td>
					</tr>
                <?php }

                    }else{
                        ?>
                        <td colspan="5"><p>Sorry, No employees to list</p></td>
                        <?php
                    }
                ?>
				</tbody>
			</table>
        </div><!-- div employees end  -->
    </div> <!-- div wrapper end  -->
    
</body>
</html>