<?php

namespace App\Http\Forms;

use Kris\LaravelFormBuilder\Form;

class StudentMotherForm extends Form
{
    public function buildForm()
    {
        $this
            ->add('mother_first_name', 'text', [
                'wrapper' => [
                    'class' => 'form-group col-sm-12 col-md-6 local-forms'
                ]
            ])
            ->add('mother_mobile', 'number', [
                'attr' => [
                    'placeholder' => 'Mobile Number',
                    'class' => 'form-control remove-number-increment'
                ],
                'wrapper' => [
                    'class' => 'form-group col-sm-12 col-md-6 local-forms'
                ]
            ])
            ->add('mother_email', 'email', [
                'wrapper' => [
                    'class' => 'form-group col-sm-12 col-md-6 local-forms'
                ]
            ])
            ->add('mother_dob', 'date', [
                'wrapper' => [
                    'class' => 'form-group col-sm-12 col-md-6 local-forms'
                ]
            ])
            ->add('mother_occupation', 'text', [
                'wrapper' => [
                    'class' => 'form-group col-sm-12 col-md-6 local-forms'
                ]
            ])
            ->add('mother_image', 'file', [
                'wrapper' => [
                    'class' => 'form-group col-sm-12 col-md-6 local-forms'
                ]
            ]);
    }
}
