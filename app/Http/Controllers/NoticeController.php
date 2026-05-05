<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\Notice;
use Illuminate\Support\Str;
use App\Events\NewNotification;
use App\Events\UserNotice;
use App\Http\Controllers\UserController;

class NoticeController extends Controller
{
    /**
     *  create notice for users
     */
    public function createNotice(Request $request)
    {
        try {
            
            $validate = Validator::make($request->all(), [
                'title' => 'required|string|max:255',
                'type' => 'required|string',
                'audience' => 'required|string',
                'html' => 'required|string',
                'customBatch' => 'nullable|exists:batches,id',
                'expiry' => 'required|date']);

            if ($validate->fails()) {
                return response()->json(['errors' => $validate->errors()], 422);
            }

            $title = $request->input('title');
            $type = $request->input('type');
            $audience = $request->input('audience');
            $customBatch = $request->input('customBatch');
            $html = $request->input('html');
            $expiry = $request->input('expiry');
            // $html = Str::markdown($html);

            $notice = Notice::create([
                "title" => $title,
                "type" => $type,
                "audience" => $audience,
                "customBatch" => $customBatch,
                "html"  => $html,
                "expiry" => $expiry
            ]);

            //sending socket notification to realtime
            switch ($audience) {
                case 'ALL':
                    $this->pushNoticeForAll("New notice is published");
                    break;
                case 'STUDENT':
                    $this->pushNotificationForStudents("New notice is published");
                    break;
                default:
                    $this->pushNotificationForFaculties("New notice is published");
                    break;
            }

            return response()->json(['message' => "notice created successfully","data"=> $notice,"status"=>1], 200);
        } catch (\Throwable $e) {
            return response()->json(['message' => 'error while craeting notice  ', 'error' => $e->getMessage()], 500);
        }
    }

    /**
     * get all notices (paginated)
     */
     public function index(Request $request)
    {
        try {
            $query = Notice::query();
            $query = Notice::with(['batches']);
            $notices = $query->orderBy('created_at', 'desc')->paginate(10);
            return response()->json([
                'status' => 1,
                'data' => $notices,
                'message' => "fetched successfully"
            ]);
        } catch (\Throwable $th) {
            return response()->json(['message' => 'error while fetching notice  ', 'error' => $th->getMessage()], 500);
        }
    }

    /**
     * find notices by name(paginated)
     */
    public function findByTitle(Request $request)
    {
        try {
            $title = $request->input('title', '');
            $notices = Notice::where('title', 'like', '%' . $title . '%')
                ->orderBy('created_at', 'desc')
                ->paginate(10);
            return response()->json([
                'status' => 1,
                'data' => $notices,
                'message' => "fetched successfully"
            ]);
        } catch (\Throwable $th) {
            return response()->json(['message' => 'error while fetching notice  ', 'error' => $th->getMessage()], 500);
        }
    }

    /**
     * get notice by id 
     */
    public function getDetailsById(Request $request, $id)
    {
        try {

            $notice = Notice::findOrFail($id);
            if ($notice) {
                 return response()->json([
                'status' => 1,
                'data' => $notice,
                'message' => "fetched successfully"
                ]);
            }
            else{
                return response()->json(['message' => 'notice not found'], 400);
            }
            
        } catch (\Throwable $th) {
            return response()->json(['message' => 'error while fetching notice details ', 'error' => $th->getMessage()], 500);
        }
    }

    /**
     * Broadcast notice
     */
    public function pushNoticeForAll($message)
    {
        
        //pushing to notification 
        event(new NewNotification($message));
    }

    /**
     * get notice for role and audience
     */
    public function getAllAllocatedNotifications(Request $request,$id)
    {
        try {
            $userController = new UserController();
            $userData = $userController->getUserById($request,$id)->getData();
            $batchId = $userData->batchId;
            $role = $userData->role;
            $finalNotice = [];
            if($role === "STAFF" || $role === "ADMIN")
            {
                $publicNotices = $this->getPublicNotices();
                $notices = [];
                $notices = $publicNotices->getData();
                foreach($notices as $n)
                {
                    $finalNotice[] = $n;   
                }

                $notices = [];
                $staffNotices = $this->getInternalNotices();
                $notices = $staffNotices->getData();
                foreach($notices as $n)
                {
                    $finalNotice[] = $n;   
                }
            }
            else{
                $publicNotices = $this->getPublicNotices();
                $notices = [];
                $notices = $publicNotices->getData();
                foreach($notices as $n)
                {
                    $finalNotice[] = $n;   
                }

                $studentNotices = $this->getStudentBatchNotices($batchId);
                $notices = [];
                $notices = $studentNotices->getData();
                foreach($notices as $n)
                {
                    $finalNotice[] = $n;   
                }
            }
            return response()->json([
                'status' => 1,
                'data' => $finalNotice,
                'message' => "fetched successfully"
            ]);
            
        } catch (\Throwable $th) {
            return response()->json(['message' => 'error while fetching notices ', 'error' => $th->getMessage()], 500);
        }
    }

    /**
     * get all public notices
     */

    public function getPublicNotices()
    {
        $notices = Notice::where('audience', 'ALL')
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json($notices);
    }


    /**
     * get all notices for staff
     */

    public function getInternalNotices()
    {
        $notices = Notice::whereIn('audience', ['STAFF', 'ADMIN'])
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json($notices);
    }

    /**
     * get all notices for a particular batch
     */
    public function getStudentBatchNotices($batchId)
    {
        $notices = Notice::where('audience', 'STUDENT')
            ->where('customBatch', $batchId)
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json($notices);
    }

    /**
     * push notice to students
     */
    public function pushNotificationForStudents($message)
    {
        event(new UserNotice($message, 'student-notice'));
    }


    /**
     * push notice to staffs
     */
    public function pushNotificationForFaculties($message)
    {
        event(new UserNotice($message, 'staff-notice'));
    }
}
