<?php 
    include("database.php");
    if(isset($_POST["consult"]))
    {
        $appointmentId = $_POST["appId"];
        $sql = "UPDATE appointments set consultation=1 where id={$appointmentId}";
        $run = mysqli_query($conn,$sql);
        echo "success";
    }
    if(isset($_POST["updatePatApt"]))
    {
        $appointmentId = $_POST["app_id"];
        $newDate = $_POST["newDate"];
        $newTime = $_POST["newslot"];
        $sql = "UPDATE `appointments` SET `date`='{$newDate}',`slot`='{$newTime}' WHERE id={$appointmentId}";
        $run = mysqli_query($conn,$sql);
        
    }
    if(isset($_POST["deleteAppointment"]))
    {
        $appointmentId= $_POST["appointmentId"];
        $sql = "DELETE FROM `appointments` WHERE id={$appointmentId}";
        $run = mysqli_query($conn,$sql);
        echo "true";
    }
    if(isset($_POST["patientPastApplications"]))
    {
        $patientNumber = $_POST["patientNumber"];
        $sql ="SELECT appointments.id,appointments.doctor_id, appointments.date, appointments.slot, appointments.patient_id, appointments.patient_name, doctors.doctor_name 
        FROM appointments 
        JOIN doctors ON appointments.doctor_id = doctors.doctor_id 
        WHERE appointments.patient_id = '{$patientNumber}' and appointments.consultation=1 
        ORDER BY appointments.date DESC, appointments.slot DESC;";
        $run = mysqli_query($conn,$sql);
        // echo $sql;
        $appointmentLists = array();
        if(mysqli_num_rows($run)>0)
        {
            while($row = mysqli_fetch_assoc($run))
            {
                $appointmentLists[] = array($row);
            }
            echo json_encode($appointmentLists);
        }
        else
        {
            echo 0;
        }
    }
    if(isset($_POST["patientApplications"]))
    {
        $patientNumber = $_POST["patientNumber"];
        $sql ="SELECT appointments.id,appointments.doctor_id, appointments.date, appointments.slot, appointments.patient_id, appointments.patient_name, doctors.doctor_name 
        FROM appointments 
        JOIN doctors ON appointments.doctor_id = doctors.doctor_id 
        WHERE appointments.patient_id = '{$patientNumber}' and appointments.consultation=0
        ORDER BY appointments.date DESC, appointments.slot DESC;";
        $run = mysqli_query($conn,$sql);
        // echo $sql;
        $appointmentLists = array();
        if(mysqli_num_rows($run)>0)
        {
            while($row = mysqli_fetch_assoc($run))
            {
                $appointmentLists[] = array($row);
            }
            echo json_encode($appointmentLists);
        }
        else
        {
            echo 0;
        }
    }
    if(isset($_POST["patientLogin"]))
    {
        $patientNumber = $_POST["patientNumber"];
        $patientPassword = $_POST["patientPassword"];

        $sql = "SELECT * FROM patients where patient_number='{$patientNumber}'";
        $run =mysqli_query($conn,$sql);
        if(mysqli_num_rows($run)>0)
        {
            $row = mysqli_fetch_assoc($run);
            $verify = password_verify($patientPassword,$row["patient_password"]);
            if($verify)
            {
                
                echo json_encode($row);
            }
            else
            {
                echo "Invalid password";
            }
        }
        else 
        {
            echo "Please register to login";
        }
    }
    if(isset($_POST["patientRegister"]))
    {
        $patientName = $_POST["patientName"];
        $patientAge = $_POST["patientAge"];
        $patientNumber = $_POST["patientNumber"];
        $patientPassword = $_POST["patientPassword"];
        $patientPassword = password_hash($patientPassword,PASSWORD_DEFAULT);
        $sql = "INSERT INTO `patients`(`patient_name`, `patient_number`, `patient_password`, `patient_age`) VALUES ('{$patientName}','{$patientNumber}','{$patientPassword}','{$patientAge}')";
        $run = mysqli_query($conn,$sql);
        echo header("Location :./index.php");
    }
    if(isset($_POST["loginDoctor"]))
    {
        $doctorID = $_POST["inputID"];
        $password = $_POST["inputPassword"];
        $sql = "SELECT doctor_id,password from doctors where doctor_id='{$doctorID}'";
        $run = mysqli_query($conn,$sql);
        if(mysqli_num_rows($run)>0) 
        {
            $row = mysqli_fetch_assoc($run);
            if($row["password"]==null)
            {
                $hashPaswd = password_hash($password,PASSWORD_DEFAULT);
                $updateSql = "UPDATE doctors set password ='{$hashPaswd}' where doctor_id='{$doctorID}'";
                $queryExec = mysqli_query($conn,$updateSql);

            }
            else
            {
                $verify = password_verify($password,$row["password"]);
                if($verify)
                {
                    echo header("Location: doctor_appointments.php?docId="."'{$doctorID}'");
                }
                else
                {
                    echo "invalid password";
                }
            }
        }
        else 
        {
            echo "Invalid id";
        }
    }
    if(isset($_POST["updateEdittime"]))
    {
        $doctor_ID = $_POST["doctor_id"];
        $selecteddate = $_POST["date"];
        $av_from_time = $_POST["av_from_time"];
        $av_to_time = $_POST["av_to_time"];
        $bk_from_time = $_POST["bk_from_time"];
        $bk_to_time = $_POST["bk_to_time"];
        $availabilityTime = $av_from_time . "-" . $av_to_time;
        $breaktime = $bk_from_time . "-" . $bk_to_time;
        
            $sql = "UPDATE `slots` SET `available_time`='{$availabilityTime}',`break_time`='{$breaktime}' WHERE date='{$selecteddate}' and doctor_id='{$doctor_ID}'";
            $run = mysqli_query($conn,$sql);
            header("Location: availability.html");
        
        
    }
    if(isset($_POST["patnum"]))
    {
        $patNum = $_POST["patnum"];
        $finalDate = $_POST["date"];
        $timeSlot = $_POST["slot"];
        $sql = "SELECT * FROM appointments where patient_id='{$patNum}' and date='{$finalDate}' and slot='{$timeSlot}'";
        $run = mysqli_query($conn,$sql);
        if(mysqli_num_rows($run)>0)
        {
            echo "fail";
        }
        else
        {
            echo $sql;
        }
    }
    if(isset($_POST["checkDate"]))
    {
        $doctor_ID = $_POST["checkDate"];
        $selecteddate = $_POST["date"];
        $sql = "SELECT * FROM slots where doctor_id='{$doctor_ID}' and date='{$selecteddate}'";
        $run = mysqli_query($conn,$sql);
        if(mysqli_num_rows($run)<=0)
        {
            echo "success";
        }
        else 
        {
            echo "fail";
        }
    }
    if(isset($_POST["getSlotDetails"]))
    {
        $doc_id = $_POST["doc_id"];
        $date = $_POST["date"];
        $sql = "SELECT * FROM slots where doctor_id='{$doc_id}' and date='{$date}'";
        $run = mysqli_query($conn,$sql);
        $slotdata = array();
        if(mysqli_num_rows($run)>0)
        {
            

            $row = mysqli_fetch_assoc($run);
            $sql = "SELECT doctor_name FROM doctors where doctor_id='{$row["doctor_id"]}'";
            $run = mysqli_query($conn,$sql);
            $nameRow = mysqli_fetch_assoc($run);
            $slotdata[] = array(
                "doctor_name"=>$nameRow["doctor_name"],
                "doctor_id"=>$row["doctor_id"],
                "date"=>$row["date"],
                "available_time"=>$row["available_time"],
                "break_time"=>$row["break_time"]
            );
            echo json_encode($slotdata);
        }
    }
    if(isset($_POST["updatetime"]))
    {
        $doctor_ID = $_POST["doctor_id"];
        $selecteddate = $_POST["date"];
        $av_from_time = $_POST["av_from_time"];
        $av_to_time = $_POST["av_to_time"];
        $bk_from_time = $_POST["bk_from_time"];
        $bk_to_time = $_POST["bk_to_time"];
        $availabilityTime = $av_from_time . "-" . $av_to_time;
        $breaktime = $bk_from_time . "-" . $bk_to_time;
        $sql = "SELECT * FROM slots where doctor_id='{$doctor_ID}' and date='{$selecteddate}'";
        $run = mysqli_query($conn,$sql);
        if(mysqli_num_rows($run)<=0)
        {
            $sql = "INSERT INTO slots ( `doctor_id`, `date`, `available_time`, `break_time`) VALUES ('{$doctor_ID}','{$selecteddate}','{$availabilityTime}','{$breaktime}')";
            $run = mysqli_query($conn,$sql);
            header("Location: ../html_pages/availability.html");
        }
        else 
        {
            echo "fail";
        }
        
        
    }
    if (isset($_POST["departmentList"]))
    {
        $listOfDepartments = array();
        $sql = "SELECT * FROM department";
        $run = mysqli_query($conn,$sql);
        if(mysqli_num_rows($run))
        {
            while($row = mysqli_fetch_assoc($run))
            {
                $listOfDepartments[] = array(
                    "dept_id"=>$row["department_id"],
                    "dept_name"=>$row["department_name"]
                );
            }
        }
        echo json_encode($listOfDepartments);
    }
    if(isset($_POST["nameList"]))
    {
        $listofnames = array();
        $sql = "SELECT * FROM doctors";
        $run = mysqli_query($conn,$sql);
        if(mysqli_num_rows($run))
        {
            while($row = mysqli_fetch_assoc($run))
            {
                $listofnames[] = array(
                    "doc_id"=>$row["doctor_id"],
                    "doc_name"=>$row["doctor_name"]
                );
            }
        }
        echo json_encode($listofnames);

    }
    if(isset($_POST['dept']))
    {
        $listofdoctors=array();
        $dept_id = $_POST['dept'];
        $sql = "SELECT * FROM doctors where dept_id=".$dept_id;
        $run = mysqli_query($conn,$sql);
        if(mysqli_num_rows($run)>0) 
        {
            
            while($row = mysqli_fetch_assoc($run))
            {
                $listofdoctors[] = array(
                    "doctor_name" => $row['doctor_name'],
                    "experience" => $row['experience'],
                    "fee" => $row['consultation_fee'],
                    "doc_id"=>$row['doctor_id']
                );
            }
        }
        echo json_encode($listofdoctors);
        
    }

    if(isset($_POST["addDoctor"]))
    {
        $name = $_POST["doc_name"];
        $id = $_POST["docid"];
        $exp = (int)$_POST["exp"];
        $fee = (int)$_POST["fee"];
        $dept_id = (int)$_POST["department"];
        echo $id;
        // Prepare the SQL statement
        $sql = "INSERT INTO `doctors` (`doctor_id`, `doctor_name`, `dept_id`, `experience`, `consultation_fee`) VALUES ('{$id}', '{$name}', $dept_id, $exp, $fee)";
        echo $sql;
        $run = mysqli_query($conn,$sql);
    }

    if(isset($_POST["doctorID"]) && isset($_POST["date"]))
    {
        $doctorID= $_POST["doctorID"];
        $date = $_POST["date"];
        $sql = "SELECT * FROM slots WHERE doctor_id='{$doctorID}' and date='{$date}'";
        
        $run = mysqli_query($conn,$sql);
        $doctorAvailability = array();
        if(mysqli_num_rows($run)>0)
        {
            $availableTime = array();
            
            while($row = mysqli_fetch_assoc($run))
            {
                $availableTime[] = array(
                    "date"=>$row["date"],
                    "available_time"=>$row["available_time"],
                    "break_time"=>$row["break_time"]
                );
                
            }
            $doctorAvailability["available"] = $availableTime;
            $sql = "SELECT * FROM appointments WHERE doctor_id='{$doctorID}' and date='{$date}'";
            $bookedTime = array();
            $run = mysqli_query($conn,$sql);
            if(mysqli_num_rows($run)>0) 
            {
                
                while($row = mysqli_fetch_assoc($run))
                {
                    $bookedTime[] = array(
                        "date"=>$row["date"],
                        "slot"=>$row["slot"],
                       
                    );
                }
                $doctorAvailability["booked"] = $bookedTime;
            }
            else
            {
                $doctorAvailability["booked"]= $bookedTime;
            }
            echo json_encode($doctorAvailability);
        }
        else
        {
            echo 0;
        }
        
    }

    if(isset($_POST["doctorid"]) && isset($_POST["date"]) && isset($_POST["slot"]))
    {
        $doctorID = $_POST["doctorid"];
        $date  = $_POST["date"];
        $slot = $_POST["slot"];
        $patID = $_POST["patID"];
        $patName = $_POST["patName"];
        $sql = "INSERT INTO `appointments` (`doctor_id`, `date`, `slot`, `patient_id`, `patient_name`) VALUES ('{$doctorID}','{$date}','{$slot}','{$patID}','{$patName}')";
        $run = mysqli_query($conn,$sql);
        echo "Booking confirmed";
    }
?>