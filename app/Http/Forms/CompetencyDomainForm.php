<?php

namespace App\Http\Forms;

use App\Models\Center;
use App\Models\Mediums;
use Kris\LaravelFormBuilder\Form;

class CompetencyDomainForm extends Form
{
    public function buildForm()
    {
        $this
            ->add('name', 'text', [
                'rules' => 'required|min:5',
            ])
            ->add('number', 'number', [
                'rules' => 'required|integer|min:1',
                'label' => 'Rank',
            ])
            // ->add('total_marks', 'number', [
            //     'rules' => 'required|integer|min:1',
            // ])
            ->add('medium_id', 'entity', [
                'class' => Mediums::class,
                'rules' => 'required',
                'label' => 'Medium',
            ]);
    }
}
