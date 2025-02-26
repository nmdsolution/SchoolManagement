<?php

namespace App\Http\Forms;

use Kris\LaravelFormBuilder\Form;

class StudentFatherForm extends Form
{
    public function buildForm()
    {
        $this
            ->add('father_first_name', 'text', [
                'wrapper' => [
                    'class' => 'form-group col-sm-12 col-md-6 local-forms'
                ]
            ])
            ->add('father_mobile', 'number', [
                'attr' => [
                    'placeholder' => 'Mobile Number',
                    'class' => 'form-control remove-number-increment'
                ],
                'wrapper' => [
                    'class' => 'form-group col-sm-12 col-md-6 local-forms'
                ]
            ])
            ->add('father_email', 'email', [
                'wrapper' => [
                    'class' => 'form-group col-sm-12 col-md-6 local-forms'
                ]
            ])
            ->add('father_dob', 'date', [
                'wrapper' => [
                    'class' => 'form-group col-sm-12 col-md-6 local-forms'
                ]
            ])
            ->add('father_occupation', 'text', [
                'wrapper' => [
                    'class' => 'form-group col-sm-12 col-md-6 local-forms'
                ]
            ])
            ->add('father_image', 'file', [
                'rules' => 'image|mimes:jpeg,png,jpg,bmp,gif,svg|max:2048',
                'wrapper' => [
                    'class' => 'form-group col-sm-12 col-md-6 local-forms'
                ]
            ]);
    }
}
