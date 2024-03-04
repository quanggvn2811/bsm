<?php

use App\Events\Backend\SystemLog;
use App\Models\Auth\User;
use App\Models\Classes;
use App\Models\ClassStudent;
use App\Models\FileUpload;
use App\Models\School;
use App\Models\Teacher;
use App\Models\Student;
use App\Models\AkerunUser;
use App\Models\TermTaxonomy;
use App\Models\AccountTerm;
use App\Services\SystemActors\ActorFactory;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\ImageManagerStatic as Image;

if (!function_exists('encrypt_role_associated')) {
    /**
     * Encrypt role and associated_id to sessionID
     *
     * @param string $role
     * @param string $associated_id
     *
     * @return string
     */
    function encrypt_role_associated($role, $associated_id)
    {
        $str = "$role:$associated_id";
        $ciphering = config('app.cipher');
        $options = 0;
        // Non-NULL Initialization Vector for encryption
        $encryption_iv = substr(config('app.mplanning_iv_crypt'), 0, 16);

        $encryption_key = config('app.mplanning_key_crypt');

        return base64_encode(openssl_encrypt(
            $str,
            $ciphering,
            $encryption_key,
            $options,
            $encryption_iv
        ));
    }
}

if (!function_exists('decrypt_associated_session')) {
    /**
     * Decrypt sessionid
     *
     * @param string $encrypted
     *
     * @return array
     */
    function decrypt_associated_session($encrypted)
    {
        //Store the cipher method
        $ciphering = config('app.cipher');
        // Use OpenSSl Encryption method
        $options = 0;
        // Non-NULL Initialization Vector for encryption
        $decryption_iv = substr(config('app.mplanning_iv_crypt'), 0, 16);

        // Store the encryption key
        $decryption_key = config('app.mplanning_key_crypt');
        $encrypted = base64_decode($encrypted);
        return openssl_decrypt(
            $encrypted,
            $ciphering,
            $decryption_key,
            $options,
            $decryption_iv
        );
    }
}

if (!function_exists('parse_info_from_associated_session')) {
    /**
     * Parse role and associated_id from associated_session
     *
     * @return mixed
     */
    function parse_info_from_associated_session()
    {
        $associated_session = request()->associated_session;
        $parsed_associated_session = Session::get('user.associated_session');
        if (isset($parsed_associated_session['session_key'])
            && $parsed_associated_session['session_key'] === $associated_session
        ) {
            return $parsed_associated_session;
        }

        if (!$associated_session) {
            return null;
        }

        $user = auth()->user();
        if (!$user || (!$user->isSchool() && !$user->isAdmin() && !$user->isClass())) {
            return null;
        }

        $decryption = decrypt_associated_session($associated_session);
        $decryption_array = explode(':', $decryption);
        $role = $decryption_array[0];
        $associated_obj = ActorFactory::createObjectUser($role);
        if ($associated_obj == null) {
            return false;
        }

        $associated_id = $decryption_array[1];
        if ($associated_obj->getModel()::find($associated_id) == null) {
            return false;
        }
        Session::put('user.associated_session.session_key', $associated_session);
        Session::put('user.associated_session.role', $role);
        Session::put('user.associated_session.associated_id', $associated_id);
        return [
            'role' => $role,
            'associated_id' => $associated_id,
        ];
    }
}

if (!function_exists('show_name')) {
    /**
     * show name shool, class, teacher, student
     *
     * @return mixed
     */
    function show_name()
    {
        $associated_session = request()->associated_session;
        if (isset($associated_session)) {
            $decryption = decrypt_associated_session($associated_session);
            $decryption_array = explode(':', $decryption);
            $role = $decryption_array[0];
            $associated_obj = ActorFactory::createObjectUser($role);
            if ($associated_obj == null) {
                return null;
            }

            $associated_id = $decryption_array[1];
            $associatedData = $associated_obj->findById($associated_id);

            if ($associatedData === null) {
                return null;
            }
            if ('teacher' == $role || 'student' == $role) {
                return $associatedData->family_name . $associatedData->first_name;
            }

            return $associatedData->name;
        }

        $user = auth()->user();
        if ($user->isSchool()) {
            $associated_obj = ActorFactory::createObjectUser('school');
            return $associated_obj->findById($user->school_id)->name;
        }

        if ($user->isClass()) {
            $associated_obj = ActorFactory::createObjectUser('class');
            return $associated_obj->findById($user->class_id)->name;
        }

        if ($user->isTeacher()) {
            $associated_obj = ActorFactory::createObjectUser('teacher');
            $associatedData = $associated_obj->findById($user->teacher_id);
            return $associatedData->family_name . $associatedData->first_name;
        }

        if ($user->isStudent()) {
            $associated_obj = ActorFactory::createObjectUser('student');
            $associatedData = $associated_obj->findById($user->student_id);
            return $associatedData->family_name . $associatedData->first_name;
        }

        return $user->account;
    }
}


if (!function_exists('update_class_owner')) {
    /**
     * updateClassOwner teacher, student
     *
     * @return mixed
     */
    function update_class_owner($request)
    {
        if ($request->get('class_owner') == null) {
            $request['class_owner'] = array(User::getClassId());
        } else {
            $classOwner = $request->get('class_owner');
            array_push($classOwner, User::getClassId());
            $request['class_owner'] = $classOwner;
        }
        return $request;
    }
}

if (!function_exists('get_user_login_info')) {

    function get_user_login_info()
    {
        $role = 'student';
        $id = '';
        $associatedSession = parse_info_from_associated_session();
        $currentUser = auth()->user();

        if (isset($associatedSession)) {
            if ($associatedSession['role'] == 'admin') {
                $role = 'admin';
            } elseif ($associatedSession['role'] == 'school') {
                $role = 'school';
            } elseif ($associatedSession['role'] == 'class') {
                $role = 'class';
            } elseif ($associatedSession['role'] == 'teacher') {
                $role = 'teacher';
            }
            $id = $associatedSession['associated_id'];
        } else {
            if ($currentUser->isAdmin()) {
                $role = 'admin';
                $id = $currentUser->id;
            } elseif ($currentUser->isSchool()) {
                $role = 'school';
                $id = $currentUser->school_id;
            } elseif ($currentUser->isClass()) {
                $role = 'class';
                $id = $currentUser->class_id;
            } elseif ($currentUser->isTeacher()) {
                $role = 'teacher';
                $id = $currentUser->teacher_id;
            } else {
                $id = $currentUser->student_id;
            }
        }
        return [
            $role,
            $id
        ];
    }
}
if (!function_exists('get_school_chat_line')) {

    function get_school_chat_line()
    {
        $associatedSession = parse_info_from_associated_session();
        $currentUser = auth()->user();
        $idSchool = '';
        if (isset($associatedSession)) {
            if ($associatedSession['role'] == 'school') {
                $idSchool = $associatedSession['associated_id'];
            } elseif ($associatedSession['role'] == 'class') {
                $classes = Classes::find($associatedSession['associated_id']);
                $idSchool = $classes->school_id;
            }
        } else {
            if ($currentUser->isSchool() || $currentUser->isClass()) {
                $idSchool = $currentUser->school_id;
            }
        }
        $schoolInfo = School::find($idSchool);
        return $schoolInfo->line_chat;
    }
}
if (!function_exists('get_account_akerun')) {

    function get_account_akerun($student)
    {
        $associatedSession = parse_info_from_associated_session();
        $accountAkerun = AkerunUser::where('mimamu_student_id', $student->id)->get()->toArray();
        return empty($accountAkerun) ? true : false;
    }
}
if (!function_exists('get_school_using_akerun')) {

    function get_school_using_akerun()
    {
        $associatedSession = parse_info_from_associated_session();
        $currentUser = auth()->user();
        $idSchool = '';
        if (isset($associatedSession)) {
            if ($associatedSession['role'] == 'school') {
                $idSchool = $associatedSession['associated_id'];
            } elseif ($associatedSession['role'] == 'class') {
                $classes = Classes::find($associatedSession['associated_id']);
                $idSchool = $classes->school_id;
            } elseif ($associatedSession['role'] == 'student') {
                $student = Student::find($associatedSession['associated_id']);
                $idClass = $student->class_id;
                $classes = Classes::find($idClass);
                $idSchool = $classes->school_id;
            }
        } else {
            if ($currentUser->isSchool() || $currentUser->isClass() || $currentUser->isStudent()) {
                $idSchool = $currentUser->school_id;
            }
        }
        $schoolInfo = School::find($idSchool);
        return $schoolInfo->is_use_akerun ?? false;
    }
}
if (!function_exists('get_school')) {

    function get_school()
    {
        $associatedSession = parse_info_from_associated_session();
        $currentUser = auth()->user();
        $idSchool = '';
        if (isset($associatedSession)) {
            if ($associatedSession['role'] == 'school') {
                $idSchool = $associatedSession['associated_id'];
            } elseif ($associatedSession['role'] == 'class') {
                $classes = Classes::find($associatedSession['associated_id']);
                $idSchool = $classes->school_id;
            } elseif ($associatedSession['role'] == 'student') {
                $student = Student::find($associatedSession['associated_id']);
                $idClass = $student->class_id;
                $classes = Classes::find($idClass);
                $idSchool = $classes->school_id;
            }
        } else {
            if ($currentUser->isSchool() || $currentUser->isClass() || $currentUser->isStudent()) {
                $idSchool = $currentUser->school_id;
            }
        }
        $schoolInfo = School::find($idSchool);
        return $schoolInfo;
    }
}
if (!function_exists('get_pah_file_insert')) {
    /**
     *
     * @return mixed
     * public/school/{school_id}/class/{class_id}/teacher/{teacher_id}/student/{student_id}{type_attachment}/{id}/{filename}
     */
    function get_pah_file_insert($type, $id, $fileName, $thumb)
    {
        $pathFile = Config::get('constants.path_folder_upload'); //public/school/
        $thumbFolder = Config::get('constants.thumbnail');
        list($role, $userId) = get_user_login_info();
        if ($role == 'admin') {
            $pathFile = Config::get('constants.path_folder_admin_upload') . $userId . '/';
        }
        if ($role == 'school') {
            $pathFile = $pathFile . $userId . '/';
        }
        if ($role == 'class') {
            $classId = $userId;
            $schoolId = Classes::where(['id' => $classId])->pluck('school_id')->first();
            $pathFile = $pathFile . $schoolId . '/class/' . $userId . '/';
        }

        switch ($type) {
            case Config::get('constants.type.logo'):
                $pathFile = $pathFile . $type . '/' . $fileName;
                break;
            case Config::get('constants.type.event'):
            case Config::get('constants.type.jouhoubox'):
            case Config::get('constants.type.interview'):
            case Config::get('constants.type.student'):
            case Config::get('constants.type.schedule'):
                $pathFile = $pathFile . $type . '/' . $id . '/' . $fileName;
                break;
            case Config::get('constants.type.communication'):
                if ($thumb) {
                    $pathFile = $pathFile . $type . '/' . $id . '/' . $thumbFolder . '/' . $fileName;
                } else {
                    $pathFile = $pathFile . $type . '/' . $id . '/' . $fileName;
                }
                break;
        }
        return $pathFile;
    }
}

if (!function_exists('upload_file')) {
    /**
     * updateClassOwner teacher, student
     *
     * @return mixed
     * $id : id of type. ex: type = event => $id = id of event
     */
    function upload_file($files, $id, $type, $titleFiles)
    {
        $indexFile = 0;
        foreach ($files as $key => $file) {
            $originName = $file->getClientOriginalName();
            $splitOriginName = explode(".", $originName);
            $extension = end($splitOriginName);

            if ($titleFiles != null) {
                $fileName = isset($titleFiles[$key]) ? $titleFiles[$key]
                : substr($originName, 0, strlen($originName) - strlen($extension) - 1);
            } else {
                $fileName = substr($originName, 0, strlen($originName) - strlen($extension) - 1);
            }

            $fileNameTime = $fileName . $indexFile . '_' . time() . '.' . $extension;
            // setting path file
            $pathFile = get_pah_file_insert($type, $id, $fileNameTime, $thumb = false);

            Storage::disk('s3')->put($pathFile, file_get_contents($file), 'public');

            $fileUpload = new FileUpload();
            $fileUpload->reference_id = $id;
            $fileUpload->type = $type;
            $fileUpload->path_file = $pathFile;
            $fileUpload->file_name = $fileName . '.' . $extension;
            $fileUpload->save();
            event(new SystemLog("Upload file [name = $fileName] success."));
            $indexFile = $indexFile + 1;
        }
    }
}

if (!function_exists('communication_upload_file')) {
    /**
     * $receiverId : id của người nhận message
     *
     * @return mixed
     *
     */
    function communication_upload_file($file, $receiverId)
    {
        $widthImgResize = 300;
        $originName = $file->getClientOriginalName();
        $splitOriginName = explode(".", $originName);
        $extension = end($splitOriginName);
        $fileName = substr($originName, 0, strlen($originName) - strlen($extension) - 1);
        $fileNameTime = $fileName . '_' . time() . '.' . $extension;
        // setting path file
        $pathFile = get_pah_file_insert(
            Config::get('constants.type.communication'),
            $receiverId,
            $fileNameTime,
            $thumb = false
        );
        $pathFileThumb = get_pah_file_insert(
            Config::get('constants.type.communication'),
            $receiverId,
            $fileNameTime,
            $thumb = true
        );
        // upload file to S3
        Storage::disk('s3')->put(
            $pathFile,
            file_get_contents($file),
            [
                'visibility' => 'public',
                'Tagging' => 'object_type=file&type=communication'
            ]
        );

        // resize image
        if (check_type_file($file) == Config::get('constants.message_type.image')) {
            $dataImg = getimagesize($file);
            $width = $dataImg[0];
            if ($width > $widthImgResize) {
                $image_resize  = Image::make($file->getRealPath())
                                ->resize($widthImgResize, null, function ($constraint) {
                                    $constraint->aspectRatio();
                                })->encode($extension);

                // upload thumb img to S3
                Storage::disk('s3')->put(
                    $pathFileThumb,
                    (string)$image_resize,
                    [
                        'visibility' => 'public',
                        'Tagging' => 'object_type=file&type=communication'
                    ]
                );
            } else {
                Storage::disk('s3')->put(
                    $pathFileThumb,
                    file_get_contents($file),
                    [
                        'visibility' => 'public',
                        'Tagging' => 'object_type=file&type=communication'
                    ]
                );
            }
        }
        return [
            'path_file' => $pathFile,
            'path_file_thumb' => $pathFileThumb,
            'file_name' => $originName,
        ];
    }
}

if (!function_exists('check_type_file')) {
    /**
     * updateClassOwner teacher, student
     *
     * @return mixed
     * $id : id of file
     */
    function check_type_file($file)
    {
        if (@is_array(getimagesize($file))) {
            return Config::get('constants.message_type.image');
        } else {
            return Config::get('constants.message_type.file');
        }
    }
}

if (!function_exists('delete_file')) {
    /**
     * updateClassOwner teacher, student
     *
     * @return mixed
     * $id : id of file
     */
    function delete_file($id)
    {
        $path = FileUpload::where('id', $id)->first()->path_file;
        Storage::disk('s3')->delete($path);
        FileUpload::findOrFail($id)->delete();
        event(new SystemLog("Delete file [id = $id] success."));
    }
}

if (!function_exists('get_logo_school')) {
    /**
     * updateClassOwner teacher, student
     *
     * @return mixed
     *
     */
    function get_logo_school()
    {
        $pathLogo = 'img/backend/brand/logo.png';
        $associatedSession = parse_info_from_associated_session();
        $currentUser = auth()->user();
        if (!$associatedSession && $currentUser == null) {
            return [
                false,
                $pathLogo,
            ];
        }
        $role = get_role_user();
        $schoolId = null;
        if (isset($associatedSession)) {
            $id = $associatedSession['associated_id'];

            switch ($role) {
                case 'admin':
                    break;
                case 'school':
                    $schoolId = $associatedSession['associated_id'];
                    break;
                case 'class':
                    $schoolId = Classes::where(['id' => $id])->pluck('school_id')->first();
                    break;
                case 'teacher':
                    $classId = Teacher::where(['id' => $id])->pluck('class_id')->first();
                    $schoolId = Classes::where(['id' => $classId])->pluck('school_id')->first();
                    break;
                case 'student':
                    $classStudents = ClassStudent::where(['student_id' => $id])->get()->toArray();
                    $classId = array_column($classStudents, 'class_id');
                    $schoolId = Classes::where(['id' => $classId[0]])->pluck('school_id')->first();
                    break;
            }
        } else {
            $schoolId = $currentUser->school_id;
        }
        if ($schoolId != null) {
            $school = School::where(['id' => $schoolId])->first();
            $fileUpload = $school->getFileUpload();
            if (sizeof($fileUpload) > 0) {
                $pathLogo = $fileUpload[0]->path_file;
                return [
                    false,
                    $pathLogo,
                ];
            }
        }
        return [
            true,
            $pathLogo,
        ];
    }
}

if (!function_exists('get_list_holiday')) {
    function get_list_holiday()
    {
        $listHoliday = config()->get('holiday');
        if (!isset($listHoliday) || empty($listHoliday)) {
            return [];
        }
        $holiday = [];
        foreach ($listHoliday as $year => $listDate) {
            foreach ($listDate as $date => $description) {
                $date = date("Y-m-d", strtotime($year . '-' . $date . ' -1 days'));
                $holiday[$date] = $description;
            }
        }
        return $holiday;
    }
}

if (!function_exists('get_file_name')) {
    function get_file_name($file)
    {
        if ($file['file_name'] != null) {
            return $file['file_name'];
        }
        $pathFile = explode('/', $file['path_file']);
        $arrNameAndExtension = array_slice($pathFile, count($pathFile) - 1);
        $nameAndExtension = implode("/", $arrNameAndExtension);
        return preg_replace('/(\_\d{10}\.)/', '.', $nameAndExtension);
    }
}

if (!function_exists('get_role_user')) {
    /**
     * updateClassOwner teacher, student
     *
     * @return mixed
     *
     */
    function get_role_user()
    {
        $role = 'student';
        $associatedSession = parse_info_from_associated_session();
        if (isset($associatedSession)) {
            $role = $associatedSession['role'];
        } else {
            if (auth()->user()->isAdmin()) {
                $role = 'admin';
            }
            if (auth()->user()->isSchool()) {
                $role = 'school';
            }
            if (auth()->user()->isClass()) {
                $role = 'class';
            }
            if (auth()->user()->isTeacher()) {
                $role = 'teacher';
            }
        }
        return $role;
    }
}

if (!function_exists('str_welcome')) {
    /**
     * Helper to grab the application welcome.
     *
     * @return mixed
     */
    function str_welcome()
    {
        $role = get_role_user();
        $associatedSession = parse_info_from_associated_session();
        $currentUser = auth()->user();
        $schoolId = '';
        if (isset($associatedSession)) {
            $id = $associatedSession['associated_id'];

            switch ($role) {
                case 'admin':
                    break;
                case 'school':
                    $schoolId = $id;
                    break;
                case 'class':
                    $schoolId = Classes::where(['id' => $id])->pluck('school_id')->first();
                    break;
                case 'teacher':
                    $classId = Teacher::where(['id' => $id])->pluck('class_id')->first();
                    $schoolId = Classes::where(['id' => $classId])->pluck('school_id')->first();
                    break;
                case 'student':
                    $classStudents = ClassStudent::where(['student_id' => $id])->get()->toArray();
                    $classId = array_column($classStudents, 'class_id');
                    $schoolId = Classes::where(['id' => $classId[0]])->pluck('school_id')->first();
                    break;
            }
        } else {
            $schoolId = $currentUser->school_id;
        }

        $strName = __('strings.backend.none_school');
        if ($schoolId) {
            $strName = '<b>' . School::where(['id' => $schoolId])->pluck('name')->first() . '</b>';
        }

        return $strName . __('strings.backend.welcome');
    }
}

if (!function_exists('get_path_logo')) {
    function get_path_logo()
    {
        list($defaultLogo, $pathLogo) = get_logo_school();
        $path = Storage::disk('s3')->url($pathLogo);
        if ($defaultLogo) {
            $path = "img/backend/brand/logo.png";
        }
        return $path;
    }
}
if (!function_exists('get_gakunen_student')) {
    function get_gakunen_student($studentId)
    {
        $studentMasters = TermTaxonomy::getStudentMasterKey();
        $termStudents = AccountTerm::where('student_id', $studentId)->get('term_id')->toArray();
        $terms = [];
        foreach ($termStudents as $term) {
            $terms[] = $term['term_id'];
        }
        $listSchoolYear = [];
        if (isset($studentMasters[\App\Models\TermTaxonomy::GAKUNEN_CD])) {
            $schoolYears = $studentMasters[\App\Models\TermTaxonomy::GAKUNEN_CD];
            $listSchoolYear = $schoolYears['terms'];
        }
        $school_year = '';
        if (isset($listSchoolYear)) {
            foreach ($listSchoolYear as $sYear) {
                if ($school_year != '') continue;
                if (isset($terms)) {
                    $school_year = in_array($sYear['id'], $terms) ? $sYear['name'] : '';
                }
            }
        }
        return $school_year;

    }
}
if (!function_exists('get_type_student')) {
    function get_type_student($studentId)
    {
        $studentMasters = TermTaxonomy::getStudentMasterKey();
        if (isset($studentMasters[\App\Models\TermTaxonomy::CHILD_KBN1])) {
            $typeStudent = $studentMasters[\App\Models\TermTaxonomy::CHILD_KBN1];
        }
        $termStudents = AccountTerm::where('student_id', $studentId)->get('term_id')->toArray();
        $terms = [];
        foreach ($termStudents as $term) {
            $terms[] = $term['term_id'];
        }

        $termDataInput = '';
        if (isset($typeStudent)) {
            if (isset($terms)) {
                foreach ($typeStudent['terms'] as $option) {
                    if ($termDataInput != '') continue;
                    $termDataInput = in_array($option['id'], $terms) ? $option['name'] : '';
                }
            }
        }
        return $termDataInput;

    }
}
if (!function_exists('arr2csv')) {
    function arr2csv($rows)
    {
        $fp = fopen('php://temp', 'r+b');
        foreach ($rows as $fields) {
            fputcsv($fp, $fields);
        }
        rewind($fp);
        // Convert CRLF
        $tmp = str_replace(PHP_EOL, "\r\n", stream_get_contents($fp));
        fclose($fp);
        // Convert row data from UTF-8 to Shift-JS
        return mb_convert_encoding($tmp, 'SJIS', 'UTF-8');
    }
}

if (!function_exists('commit_version')) {
    function commit_version()
    {
        // return '?v=' . time();
        return '?v=' . config('app.commit_version');
    }
}
