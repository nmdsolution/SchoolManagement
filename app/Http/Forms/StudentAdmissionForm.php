<?php

namespace App\Http\Forms;

use App\Models\ClassSchool;
use App\Models\ClassSection;
use App\Models\Competency\PrimaryClass;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Kris\LaravelFormBuilder\Form;
use Kris\LaravelFormBuilder\Field;

class StudentAdmissionForm extends Form
{
    public function buildForm()
    {
        $admission_no = mt_rand(100000, 999999);

        $nationalities = collect([
            'cameroon',
            'chad',
            'central_africa',
            'equatorial_guinea',
            'gabon', 
            'congo',
            'nigeria'
        ])->mapWithKeys(function ($pays) {
            return [$pays => Str::title(str_replace('_', ' ', $pays))];
        })->toArray();

        $statuses = collect(["Not applicable", "Handicap", "Refugee", "Orphan"])
            ->mapWithKeys(function ($status) {
                return [$status => __($status)];
            })->toArray();

        $this
            ->add('first_name', Field::TEXT, [
                'rules' => 'required',
                'wrapper' => [
                    'class' => 'form-group col-sm-12 col-md-6 local-forms'
                ],
                'attr' => [
                    'required' => 'required'
                ]
            ])
            ->add('dob', 'date', [
                'rules' => 'required',
                'attr' => [
                    'class' => 'form-control'
                ],
                'wrapper' => [
                    'class' => 'form-group col-sm-12 col-md-6 local-forms'
                ]
            ])
            ->add('gender', Field::SELECT, [
                'rules' => 'required',
                'choices' => [
                    'male' => __('Male'),
                    'female' => __('Female')
                ],
                'empty_value' => __('Choose gender'),
                'wrapper' => [
                    'class' => 'form-group col-sm-12 col-md-6 local-forms'
                ]
            ])
            ->add('born_at', 'text', [
                'rules' => 'required',
                'wrapper' => [
                    'class' => 'form-group col-sm-12 col-md-6 local-forms'
                ]
            ])
            ->add('image', Field::FILE, [
                'rules' => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048',
                'wrapper' => [
                    'class' => 'form-group col-sm-12 col-md-6 local-forms local-forms-files'
                ]
            ])
            ->add('minisec_matricule', 'text', [
                'rules' => 'string|min:6|max:6',
                'wrapper' => [
                    'class' => 'form-group col-sm-12 col-md-6 local-forms'
                ]
            ])
            ->add('nationality', 'select', [
                'rules' => 'required',
                'choices' => $nationalities,
                'empty_value' => __('Choose nationality'),
                'wrapper' => [
                    'class' => 'form-group col-sm-12 col-md-6 local-forms'
                ]
            ]);

            if(Auth::user()->center->type === 'primary') {
                $classSection = ClassSection::owner()->with('class')->whereHas('class', function($q) {
                    
                })->first();
                $this->add('class_id', 'entity', [
                    'rules' => 'required|exists:primary_classes,id',
                    'wrapper' => [
                        'class' => 'form-group col-sm-12 col-md-6 local-forms'
                    ],
                    'class' => ClassSchool::class,
                    'property' => 'name',
                    'query_builder' => function (ClassSchool $query) {
                        return $query->owner()->get();
                    },
                    'label' => __('class')
                ])
                ->add('class_section_id', Field::HIDDEN, [
                    'value' => $classSection->id
                ])
                ;
            } else {
                $this->add('class_section_id', 'entity', [
                    'class' => ClassSection::class,
                    'rules' => 'required',
                    'wrapper' => [
                        'class' => 'form-group col-sm-12 col-md-6 local-forms'
                    ],
                    'query_builder' => function (ClassSection $query) {
                        return $query->owner()->with('class.stream', 'section')->whereHas('class', function ($q) {
                            $q->activeMediumOnly();
                        })->get();
                    }
                ]);
            }

            
            $this
            ->add('status', Field::CHOICE, [
                'rules' => 'required|array',
                'choices' => $statuses,
                'attr' => [
                    'class' => 'form-control select2',
                    'multiple' => 'multiple',
                    'style' => 'width: 100%',
                ],
                'label' => __('status'),
                'wrapper' => [
                    'class' => 'form-group col-sm-12 col-md-6 local-forms'
                ]
            ])
            ->add('repeater', Field::CHOICE, [
                'rules' => 'required',
                'choices' => [
                    1 => __('Yes'),
                    0 => __('No')
                ],
                'wrapper' => [
                    'class' => 'form-group col-sm-12 col-md-6 local-forms'
                ]
            ])
            ->add('admission_no', 'text', [
                'rules' => 'required',
                'value' => $admission_no,
                'wrapper' => [
                    'class' => 'form-group col-sm-12 col-md-6 local-forms'
                ]
            ])
            ->add('admission_date', 'date', [
                'rules' => 'required',
                'attr' => [
                    'class' => 'form-control'
                ],
                'wrapper' => [
                    'class' => 'form-group col-sm-12 col-md-6 local-forms'
                ]
            ]);

            $this->compose(StudentFatherForm::class);
            $this->compose(StudentMotherForm::class);

            $this
            ->add('submit', 'submit', [
                'value' => __('Submit'),
                'attr' => [
                    'class' => 'btn btn-primary btn-block'
                ],
                'wrapper' => [
                    'class' => 'form-group col-12 local-forms'
                ]
            ]);
    }
}
