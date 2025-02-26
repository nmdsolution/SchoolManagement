<?php

namespace App\Http\Forms;

use Kris\LaravelFormBuilder\Form;
use App\Models\Competency\CompetencyDomain;
use Kris\LaravelFormBuilder\Field;

class ClassCompetencyForm extends Form
{
    public function buildForm()
    {
        $this
            ->add('competencies', 'collection', [
                'type' => 'form',
                'options' => [
                    'class' => CompetencyForm::class,
                    'label' => false,
                ],
            ]);
    }
}
