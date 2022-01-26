<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Doctor;

use App\Models\Appointment;

use App\Models\User;

use Notification;

use App\Notifications\MyFirstNotification;
use Illuminate\Support\Facades\Auth;

class AdminController extends Controller
{
    public function addview()
    {
        if(Auth::id())
        {
            if(Auth::user()->usertype==1)
            {
                return view('admin.add_doctor');
            }

            else
            {
                return redirect()->back();
            }
        }

        else
        {
            return redirect('login');
        }

        
    }

    public function upload(Request $request)
    {
        $doctors=new doctor;

        $image=$request->file;

        $imagename=time().'.'.$image->getClientoriginalExtension();

        $request->file->move('doctorimage',$imagename);

        $doctors->image=$imagename;

        $doctors->name=$request->name;

        $doctors->phone=$request->number;

        $doctors->room=$request->room;

        $doctors->specialty=$request->specialty;

        $doctors->save();

        return redirect()->back()->with('message','Doctor Added Succesfully');
    }

    public function showappointment()
    {
        if(Auth::id())

        {
            if(Auth::user()->usertype==1)
            {

               $data=appointment::all();

               return view('admin.showappointment',compact('data'));
            }
            else
            {
               return redirect()->back();
            }
        }
        else
        {
            return redirect('login');
        }
    }

    public function approved($id)
    {
        $data=appointment::find($id);

        $data->status='approved';

        $data->save();

        return redirect()->back();
    }

    public function canceled($id)
    {
        $data=appointment::find($id);

        $data->status='canceled';

        $data->save();

        return redirect()->back();
    }

    public function showdoctor()
    {
        if(Auth::id())

        {
            if(Auth::user()->usertype==1)
            {
              $data = doctor::all();
              return view('admin.showdoctor',compact('data'));
            }
            else
            {
               return redirect()->back();
            }
        }
        else
        {
            return redirect('login');
        }
        
    }

    public function deletedoctor($id)
    {
        $data=doctor::find($id);

        $data->delete();

        return redirect()->back();
    }

    public function updatedoctor($id)
    {
        $data = doctor::find($id);
        return view('admin.update_doctor',compact('data'));
    }

    public function editdoctor(Request $request, $id)
    {
        $doctor = doctor::find($id);

        $doctor->name=$request->name;

        $doctor->phone=$request->phone;

        $doctor->specialty=$request->specialty;

        $doctor->room=$request->room;

        $image=$request->file;

        if($image)
        {

        $imagename=time().'.'.$image->getClientOriginalExtension();

        $request->file->move('doctorimage',$imagename);

        $doctor->image=$imagename;

        }

        $doctor->save();

        return redirect()->back()->with('message','Doctor Details Update Successfully');


    }

    public function emailview($id)
    {
        $data=appointment::find($id);

        return view('admin.email_view',compact('data'));
    }

    public function sendemail(Request $request,$id)
    {
        $data = appointment::find($id);

        $details=[

            'greeting' => $request->greeting,

            'body' => $request->body,

            'actiontext' => $request->actiontext,

            'actionurl' => $request->actionurl,

            'endpart' => $request->endpart
        ];

        Notification::send($data,new MyFirstNotification($details));

        return redirect()->back()->with('message','Email send successfully');
    }
}
