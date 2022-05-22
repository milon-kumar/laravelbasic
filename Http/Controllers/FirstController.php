<?php


namespace App\Http\Controllers;


use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use PHPUnit\TextUI\XmlConfiguration\Group;
use Yajra\DataTables\DataTables;

class FirstController extends Controller{

    public function index(){
//        $users = User::get();
//        return view("welcome", compact("users"));
       $users = DB::table("users")->select("name", "email")->get();


        return view("welcome", compact("users"));
    }

    public function getAllUser(Request $request){
        $users = User::orderBy("id", "desc")->get();

        return DataTables::of($users)->addColumn('action', function ($q){
            $id = encrypt($q->id);
            '
                <a href="javascript:void(0)" data-toggle="modal" data-id="' . $id . '" data-target=".add_modal" class="btn btn-success btn-sm openaddmodal" ><i class="fas fa-pencil-alt"></i></a>
                <a href="javascript:void(0)" data-toggle="modal" data-id="' . $id . '" class="btn btn-danger btn-sm delete_record" ><i class="fas fa-trash-alt"></i></a>
                <a href="javascript:void(0)"  data-id="' . $id . '" class="btn btn-info btn-sm publishToProfile" ><i class="fab fa-facebook-square"></i></a>';
        })->addColumn('name', function ($q) {
            return $q->name;
        })->addColumn('email', function ($q) {
            return $q->email;
        })->addColumn('created_at', function ($q) {
            return $q->created_at;
        })->addColumn('updated_at', function ($q) {
            return $q->updated_at;
        });






    }





    public function search_tutors_by_matching($queris){

        // if($queris[0] != null){
        // return $queris;
        // }
        // return $queris[0];
        // exit();

        // exit();
        $t=microtime(true);
        // $tutors=Tutor::with(['tutor_personal_information','courses','prefered_locations','categories','course_subjects','tutor_degrees'=>function($q){
        //     return $q->whereIn('degree_id',[3,4]);
        // }])->get();
        $offer=$this;
        // dd($offer);


        $tutors=Tutor::where("is_sms", 1);
        // $tutors=Tutor::orderBy('is_premium','desc');


        $tutors=$tutors->whereHas('city',function($q){
            return $q->where('id',$this->city_id);
        });

        $tutors=$tutors->whereHas('pref_locations',function($q){
            return $q->where('location_id',$this->location_id);
        });


        if($offer->tutor_gender!=null || $offer->tutor_religion_id!=null){
            $tutors=$tutors->whereHas('tutor_personal_information',function($q){
                if($this->tutor_gender!=null && $this->tutor_religion_id!=null){
                    return $q->where('gender',$this->tutor_gender)->where('religion_id',$this->tutor_religion_id);
                }elseif($this->tutor_gender!=null){
                    return $q->where('gender',$this->tutor_gender);
                }else{
                    return $q->where('religion_id',$this->tutor_religion_id);
                }
            });
        }



        if($offer->tutor_category_id!=null){
            $tutors=$tutors->whereHas('categories',function($query){
                return $query->where('id',$this->tutor_category_id);
            });
        }





        if($this->group!=null || $this->tutor_school_id!=null || $this->curriculum_id!=null){
            $tutors= $tutors->whereHas('tutor_degrees',function($q){
                $q=$q->where('degree_id',6);
                if($this->group!=null){
                    // dd($this->group);
                    $q=$q->where('group_or_major','like',"%".$this->group."%");
                }
                if($this->tutor_school_id!=null){
                    $q=$q->where('institute_id',$this->tutor_school_id);
                }
                if($this->curriculum_id!=null){
                    $q=$q->where('curriculum_id',$this->curriculum_id);
                }
                return $q;
            });
        }




        if($this->tutor_college_id!=null){
            $tutors= $tutors->whereHas('tutor_degrees',function($q){
                $q=$q->where(function($q){
                    return $q->where('degree_id',5)->where('institute_id',$this->tutor_college_id);
                });
            });
        }


        if($this->city_id)
            if($this->tutor_departments->count()>0 || $this->tutor_universities->count()>0 || $this->university_type!=null){
                $tutors= $tutors->whereHas('tutor_degrees',function($q){
                    if($this->tutor_departments->count()>0){
                        $q=$q->where(function($q){
                            $titles=[];
                            foreach($this->tutor_departments as $td){
                                $titles[]=$td->title;
                            }
                            return $q->where('degree_id',4)->whereIn('department',$titles);
                        });
                    }

                    if($this->tutor_universities->count()>0){
                        $q=$q->where(function($q){
                            $ids=[];
                            foreach($this->tutor_universities as $tu){
                                $ids[]=$tu->id;
                            }
                            return $q->where('degree_id',4)->whereIn('institute_id',$ids);
                        });
                    }
                    if($this->university_type!=null){
                        $q=$q->where(function($q){
                            return $q->where('degree_id',4)->where('university_type',$this->university_type);
                        });
                    }
                    return $q;
                });
            }



        if($queris[1]=="top"){
            if(count($tutors->get()) > 100){
                $tutors->orderBy("id", "ASC")->take(100);
                $tutors = $tutors->with(['courses','course_subjects','teaching_methods'])->get();
            }else{
                $tutors = $tutors->orderBy("id", "ASC")->with(['courses','course_subjects','teaching_methods'])->get();
            }
        }else if($queris[1]=="bottom"){
            if(count($tutors->get()) > 100){
                $tutors = $tutors->orderBy("id", "DESC")->take(100);
                $tutors = $tutors->with(['courses','course_subjects','teaching_methods'])->get();
            }else{
                $tutors = $tutors->orderBy("id", "DESC");
                $tutors = $tutors->with(['courses','course_subjects','teaching_methods'])->get();
            }
        }else if($queris[1]=="latest"){
            $tutors = $tutors->orderBy("created_at", "DESC")->latest()->take(50);
            $tutors = $tutors->with(['courses','course_subjects','teaching_methods'])->get();
        }else if($queris[1]=="by_class"){
            $tutors= $tutors->whereHas('courses',function($query){
                return $query->where('id',$this->course_id);
            });
            $tutors = $tutors->with(['courses','course_subjects','teaching_methods'])->get();
        }else{
            $tutors = $tutors->with(['courses','course_subjects','teaching_methods'])->get();
        }




        $applications=$this->applications;
        $offer_cat=$offer->category;
        $offer_course_subjects=$offer->course_subjects;
        $tutor_array=[];

        foreach($tutors as $tutor){
            $parcent=0;
            // $personal=$tutor->tutor_personal_information;
            // $u_degrees=$tutor->tutor_degrees()->whereIn('degree_id',[3,4])->get();
            // $u_degrees=$tutor->tutor_degrees;
            $cat_matched=false;
            $gender_matched=false;
            $university_matched=false;
            $city_matched=false;
            $department_matched=false;
            $study_type_matched=false;
            $university_type_matched=false;

            // foreach($tutor->categories as $category){
            //     if($offer_cat->id==$category->id){
            //         $cat_matched=true;
            //         break;
            //     }
            // }

            // if($personal!=null && $personal->gender==$offer->tutor_gender){
            //     $gender_matched=true;
            // }

            // foreach($u_degrees as $degree){
            //     if($degree->institute_id==$offer->tutor_university_id){
            //         $university_matched=true;
            //         break;
            //     }
            //     if($degree->university_type==$offer->university_type){
            //         $university_type_matched=true;
            //     }
            //     if($degree->study_type_id==$offer->tutor_study_type_id){
            //         $study_type_matched=true;
            //     }
            //     if($degree->department==$offer->tutor_department){
            //         $department_matched=true;
            //     }
            // }


            // if($tutor->city_id==$offer->city_id){
            //     $city_matched=true;
            // }
            // if($offer->tutor_department==null){
            //     $department_matched=true;
            // }
            // if($offer->tutor_study_type_id==null){
            //     $study_type_matched=true;
            // }
            // if($offer->university_type==null){
            //     $university_type_matched=true;
            // }
            // if($cat_matched && $gender_matched && $university_matched && $city_matched && $department_matched && $study_type_matched && $university_type_matched){
            //     $parcent+=50;
            // }


            if($offer->location_id==$tutor->location_id){
                $parcent+=20;
            }



            // foreach($tutor->pref_locations as $pref_loc){
            //     if($offer->location_id==$pref_loc->location_id){
            //         $parcent+=20;
            //         break;
            //     }
            // }

            // //Parcent for location;
            // $parcent+=20;
            foreach($tutor->courses as $cs){
                if($cs->id==$offer->course_id){
                    $parcent+=45;
                }
            }

            $ocs=$offer_course_subjects;
            $ocs_count=$ocs->count();
            $ocs_match_found=0;
            $tcs= $tutor->course_subjects;
            foreach($ocs as $oc){
                foreach($tcs as $tc){
                    if($oc->pivot->course_subject_id==$tc->pivot->course_subject_id){
                        $ocs_match_found+=1;
                        break;
                    }
                }
            }
            if($ocs_count>0){
                $ocs_percent=($ocs_match_found / $ocs_count) * 100;
            }else{
                $ocs_percent=0;
            }
            $parcent+= (25/100) * $ocs_percent;

            // if($personal!=null && $personal->gender==$offer->student_gender){
            //     $parcent+=5;
            // }
            if($tutor->tutoring_experience!=null){
                $parcent+=8;
            }
            foreach($tutor->teaching_methods as $tm){
                if($this->teaching_method_id==$tm->id){
                    $parcent+=2;
                    break;
                }
            }
            // if($parcent>=50){

            // }
            $tutor->mathcing_rate=$parcent;
            foreach($applications as $app){
                if($app->tutor_id=$tutor->id){
                    $tutor->applied=true;
                    break;
                }
            }
            $tutor_array[]=$tutor;
        }
        $tutor_count=count($tutor_array);
        // \dump($tutor_count);
        // dd(microtime(true)-$t);
        for($i=0;$i<$tutor_count;$i++){
            for($j=$i+1; $j < $tutor_count;$j++){
                if($tutor_array[$i]->mathcing_rate < $tutor_array[$j]->mathcing_rate){
                    $temp=$tutor_array[$i];
                    $tutor_array[$i]=$tutor_array[$j];
                    $tutor_array[$j]=$temp;
                }
            }
        }
        $premium_tutors=[];
        $tutors=[];
        foreach($tutor_array as $tutor){
            if($tutor->is_premium==1){
                $premium_tutors[]=$tutor;
            }else{
                $tutors[]=$tutor;
            }
        }
        // dd(array_merge($premium_tutors,$tutors)[0]);
        // return array_merge($premium_tutors,$tutors);


        // return $queris;

        if($queris[1]=="true"){
            return $premium_tutors;
        }else if($queris[1]=="all"){
            return array_merge($premium_tutors,$tutors);
        }else{
            return $tutors;
        }


    }

}
