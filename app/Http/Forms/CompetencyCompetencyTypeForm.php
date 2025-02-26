<?php

namespace App\Http\Forms;

use Kris\LaravelFormBuilder\Form;

class CompetencyCompetencyTypeForm extends Form
{
    public function buildForm()
    {
        $this->add('competency_type_id', 'checkbox')
            ->add('competency_id', 'hidden', [
                'rules' => [
                    'required',
                ],
            ])
            ->add('total_marks', 'number', [
                'rules' => 'required|numeric|min:0',
                'attr' => [
                    'placeholder' => 'Total Marks',
                ],
            ])
            ;
    }
}
