<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "medical_diagnosis";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Function to calculate risk percentage
function calculateRisk($diseaseSymptoms, $userSymptoms) {
    $totalSymptoms = count($diseaseSymptoms);
    $matchedSymptoms = 0;

    foreach ($diseaseSymptoms as $symptom) {
        if ($userSymptoms[$symptom] == 1) {
            $matchedSymptoms++;
        }
    }

    return ($matchedSymptoms / $totalSymptoms) * 100;
}

// Define symptoms for each disease
$tuberculosisSymptoms = ['cough', 'weight_loss', 'night_sweats', 'chest_pain'];
$sinusitisSymptoms = ['headache', 'facial_pain', 'nasal_congestion', 'runny_nose'];
$influenzaSymptoms = ['fever', 'sore_throat', 'muscle_pain', 'fatigue'];
$asthmaSymptoms = ['wheezing', 'shortness_of_breath', 'chest_tightness', 'cough'];

// Handle user input
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['name'];
    $ide = $_POST['ide'];
    $age = $_POST['age'];
    $gender = $_POST['gender'];


    // Collect user symptom data
    $userSymptoms = [
        'cough' => isset($_POST['cough']) ?  1 : 0, 
        'weight_loss' => isset($_POST['weight_loss']) ? 1 : 0,
        'night_sweats' => isset($_POST['night_sweats']) ? 1 : 0,
        'chest_pain' => isset($_POST['chest_pain']) ? 1 : 0,
        'headache' => isset($_POST['headache']) ? 1 : 0,
        'facial_pain' => isset($_POST['facial_pain']) ? 1 : 0,
        'nasal_congestion' => isset($_POST['nasal_congestion']) ? 1 : 0,
        'runny_nose' => isset($_POST['runny_nose']) ? 1 : 0,
        'fever' => isset($_POST['fever']) ? 1 : 0,
        'sore_throat' => isset($_POST['sore_throat']) ? 1 : 0,
        'muscle_pain' => isset($_POST['muscle_pain']) ? 1 : 0,
        'fatigue' => isset($_POST['fatigue']) ? 1 : 0,
        'wheezing' => isset($_POST['wheezing']) ? 1 : 0,
        'shortness_of_breath' => isset($_POST['shortness_of_breath']) ? 1 : 0,
        'chest_tightness' => isset($_POST['chest_tightness']) ? 1 : 0,
    ];

    // Calculate risk percentages for each disease
    $tuberculosisRisk = calculateRisk($tuberculosisSymptoms, $userSymptoms);
    $sinusitisRisk = calculateRisk($sinusitisSymptoms, $userSymptoms);
    $influenzaRisk = calculateRisk($influenzaSymptoms, $userSymptoms);
    $asthmaRisk = calculateRisk($asthmaSymptoms, $userSymptoms);

    // Determine highest risk diagnosis
    //$diagnosis = "No diagnosis available based on symptoms.";
    $highestRisk = max($tuberculosisRisk, $sinusitisRisk, $influenzaRisk, $asthmaRisk);

    if ($highestRisk == $tuberculosisRisk && $tuberculosisRisk > 0) {
        $diagnosis = "Tuberculosis";
    } elseif ($highestRisk == $sinusitisRisk && $sinusitisRisk > 0) {
        $diagnosis = "Sinusitis";
    } elseif ($highestRisk == $influenzaRisk && $influenzaRisk > 0) {
        $diagnosis = "Influenza";
    } elseif ($highestRisk == $asthmaRisk && $asthmaRisk > 0) {
        $diagnosis = "Asthma";
    }

    // Save to database
    $stmt = $conn->prepare("INSERT INTO patients (name, cough, weight_loss, night_sweats, chest_pain, headache, facial_pain, nasal_congestion, runny_nose, fever, sore_throat, muscle_pain, fatigue, wheezing, shortness_of_breath, chest_tightness,diagnosis,age,ide,gender) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?,?,?,?)");
    $stmt->bind_param("siiiiiiiiiiiiiiiiiis", $name, $userSymptoms['cough'], $userSymptoms['weight_loss'], $userSymptoms['night_sweats'], $userSymptoms['chest_pain'], $userSymptoms['headache'], $userSymptoms['facial_pain'], $userSymptoms['nasal_congestion'], $userSymptoms['runny_nose'], $userSymptoms['fever'], $userSymptoms['sore_throat'], $userSymptoms['muscle_pain'], $userSymptoms['fatigue'], $userSymptoms['wheezing'], $userSymptoms['shortness_of_breath'], $userSymptoms['chest_tightness'], $diagnosis , $age, $ide, $gender);
    $stmt->execute();
    $stmt->close();

    echo "Diagnosis for $name: $diagnosis<br>";
    echo "Tuberculosis Risk: $tuberculosisRisk%<br>";
    echo "Sinusitis Risk: $sinusitisRisk%<br>";
    echo "Influenza Risk: $influenzaRisk%<br>";
    echo "Asthma Risk: $asthmaRisk%<br>";
}
  
$conn->close();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Medical Diagnosis System</title>
</head>
<body>
    <h2>Enter Patient Information</h2>
    <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
        Name: <input type="text" name="name" required><br><br>
        ID   :<input type="number" name="ide" required><br><br>
        Age :<input type="number" name="age" required><br><br>
        Gender:<input type="text" name="gender" required><br><br> 
        <h3>Symptoms:</h3>
        Cough: <input type="checkbox" name="cough"><br>
        Weight Loss: <input type="checkbox" name="weight_loss"><br>
        Night Sweats: <input type="checkbox" name="night_sweats"><br>
        Chest Pain: <input type="checkbox" name="chest_pain"><br>
        Headache: <input type="checkbox" name="headache"><br>
        Facial Pain: <input type="checkbox" name="facial_pain"><br>
        Nasal Congestion: <input type="checkbox" name="nasal_congestion"><br>
        Runny Nose: <input type="checkbox" name="runny_nose"><br>
        Fever: <input type="checkbox" name="fever"><br>
        Sore Throat: <input type="checkbox" name="sore_throat"><br>
        Muscle Pain: <input type="checkbox" name="muscle_pain"><br>
        Fatigue: <input type="checkbox" name="fatigue"><br>
        Wheezing: <input type="checkbox" name="wheezing"><br>
        Shortness of Breath: <input type="checkbox" name="shortness_of_breath"><br>
        Chest Tightness: <input type="checkbox" name="chest_tightness"><br><br>
        <input type="submit" value="Diagnose">
    </form>
    
</body>
</html>
