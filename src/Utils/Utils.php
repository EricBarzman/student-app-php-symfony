<?php

namespace App\Utils;

use App\Entity\Student;
use Exception;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\String\Slugger\SluggerInterface;

class Utils
{
    public function __construct(
        private string $targetDirectory,
        private SluggerInterface $slugger
    ) {}


    /**
     * @param $photo64string Upload photo from 64 string
     */
    public function uploadPhotoFromString(string $photo64string)
    {
        $data = explode(';base64,', $photo64string);
        $decodedData = base64_decode($data[1]);
        $fileName = uniqid().'.jpeg';
        $filePath = 'uploads/photos/'.$fileName;
        file_put_contents($filePath, $decodedData);
        return $fileName;
    }

    /**
     * @param $file Upload photo file, store it in folder created through constructor
     */
    public function upload(UploadedFile $file) : string
    {
        $originalFilename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
        $safeFilename = $this->slugger->slug($originalFilename);
        $newFilename = $safeFilename.'-'.uniqid().'.'.$file->guessExtension();
        try {
            $file->move($this->getTargetDirectory(), $newFilename);
        } catch (FileException $e) {
            // Do something if problem
        }
        return $newFilename;
    }

    public function getTargetDirectory(): string
    {
        return $this->targetDirectory;
    }

    /**
     * @param $lastCreatedIdString Generate a new student ID string, increased by one, from a previous one
     */
    public function generateNewStudentIdFromLatestId(string $lastCreatedIdString) {
        if ($lastCreatedIdString == null)
            return $this->convertNumberToStudentIdString(1);
        $newNumber = $this->convertStudentIdStringToNumber($lastCreatedIdString) + 1;
        return $this->convertNumberToStudentIdString($newNumber);
    }
    
    /**
     * Convert a student ID string, S and several digits, to a number
     */
    public function convertStudentIdStringToNumber(string $string) {
        $newStr = substr($string, 1);
        $number = intval($newStr);
        return $number;
    }

    /**
     * @param $number Convert a number to a student ID
     * @param $length Length can be chosen here.
     */
    public function convertNumberToStudentIdString(int $number, int $length = 6) {
        $strNumber = strval($number);
        if (strlen($strNumber) > $length)
            return 'Error : Number must no have more than '.$length.' digits';
        $string = '';
        for ($i = 0; $i < $length; $i++) {
            if ($i < strlen($strNumber)) $string = $strNumber[strlen($strNumber) - 1 - $i].$string;
            if ($i >= strlen($strNumber)) $string = '0'.$string;
        }
        return 'S'.$string;
    }

    /**
     * @param $length Generate a random pin code of chosen length
     */
    public function generateRandomPincode(int $length = 6) {
        $char = "0123456789";
        $charLength = strlen($char);
        $randomString = '';
        for ($i = 0; $i < $length; $i++ ) {
            $randomString .= $char[random_int(0, $charLength - 1)];
        }   
        return $randomString;
    }

    /**
     * @param $student Retrieve a student's current class, depending on the current time
     */
    public function retrieveCurrentClass(Student $student): string {
        if (!$student->getSchoolClass()->getTimetable()) {
            return '';
        }
        if (!$student->getSchoolClass()->getTimetable()->getTimetable()) {
            return '';
        }

        $todayClasses = $student->getSchoolClass()->getTimetable()->getTimetable()[date('l')];
        $hourNow = date("G");
        if ($hourNow < 8) return '';
        if ($hourNow > 18) return '';
        return $todayClasses[$hourNow];
    }

    /**
     * Retrieve a student's next class today, always at least the next starting hour.
     * If the search is made before 8am, the function will cap the time to 7am.
     * It won't search after 5pm.
     */
    public function retrieveNextClass(Student $student) {
        if ($student->getSchoolClass()->getTimetable()) {
            if ($student->getSchoolClass()->getTimetable()->getTimetable())
                $todayClasses = $student->getSchoolClass()->getTimetable()->getTimetable()[date('l')];
        }
        
        // Start checking one hour from now
        $nextHour = date("G") + 1;
        // If we check in the morning, it will consider it is 7am (and start searching at 8am)
        if ($nextHour < 8) $nextHour = 8;

        $nextClassToFind = "";
        while ($nextClassToFind == "" && $nextHour <= 18) {    
            try {
                $nextClassToFind = $todayClasses[$nextHour];
                $nextHour++;
            } catch(Exception $e) { return ['', '']; }
        }

        if ($nextClassToFind) {
            $nextHour--;
            // Convert 24h time to am/pm
            $hourAMPM = '';
            if ($nextHour < 12) $hourAMPM = $nextHour.'am';
            if ($nextHour == 12) $hourAMPM = $nextHour.'pm';
            if ($nextHour > 12) $hourAMPM = ($nextHour - 12).'pm';
            return [$nextClassToFind, $hourAMPM];
        } 
        return ['', ''];
    }
}