<?php

namespace App\Http\Forms;

use App\Models\ClassSchool;
use Kris\LaravelFormBuilder\Form;
use Kris\LaravelFormBuilder\Field;
use App\Models\Competency\CompetencyDomain;

class CompetencyForm extends Form
{
    public function buildForm()
    {
        $this
            ->add('name', 'text')
            ->add('competency_domain_id', 'entity', [
                'class' => CompetencyDomain::class,
                'query_builder' => function (CompetencyDomain $query) {
                    return $query->owner()->get();
                },
            ])
            // ->add('classes', 'entity', [
            //     'class' => ClassSchool::class,
            //     'query_builder' => function (ClassSchool $query) {
            //         return $query->owner()->get();
            //     },
            //     'multiple' => true,
            //     'expanded' => true,
            //     'attr' => ['class' => 'form-control'],
            //     'label' => __('Associate Classes'),
            // ])
            // ->add('submit', Field::BUTTON_SUBMIT, [
            //     'label' => 'Enregistrer',
            //     'attr' => ['class' => 'btn btn-primary btn-block'],
            // ])
            ;
    }
}
