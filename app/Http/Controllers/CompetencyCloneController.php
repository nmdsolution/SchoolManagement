<?php

namespace App\Http\Controllers;

use App\Models\ClassSchool;
use App\Models\Competency\Competency;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CompetencyCloneController extends Controller
{
    public function cloneCompetencies(Request $request)
    {
        $request->validate([
            'source_class_id' => 'required|exists:classes,id',
            'target_class_id' => 'required|exists:classes,id',
        ]);

        try {
            DB::beginTransaction();

            $sourceClass = ClassSchool::findOrFail($request->source_class_id);
            $targetClass = ClassSchool::findOrFail($request->target_class_id);

            // Récupérer toutes les compétences de la classe source
            $sourceCompetencies = $sourceClass->competencies()->get();

            // Pour chaque compétence de la classe source
            foreach ($sourceCompetencies as $competency) {
                // Vérifier si la compétence n'existe pas déjà dans la classe cible
                if (!$targetClass->competencies()->where('competency_id', $competency->id)->exists()) {
                    // Cloner la relation avec les mêmes attributs
                    $targetClass->competencies()->attach($competency->id, [
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            }

            DB::commit();

            return response()->json([
                'message' => 'Les compétences ont été clonées avec succès',
                'source_class' => $sourceClass->name,
                'target_class' => $targetClass->name,
                'competencies_count' => $sourceCompetencies->count()
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Une erreur est survenue lors du clonage des compétences',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
