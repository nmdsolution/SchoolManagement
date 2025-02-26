<?php
namespace App\Http\Controllers\Competency;

use App\Models\ClassSchool;
use Illuminate\Http\Request;
use Illuminate\Routing\Route;
use App\Http\Controllers\Controller;
use App\Models\Competency\ClassCompetency;
use Illuminate\Support\Facades\Auth;
use App\Models\Competency\Competency;
use App\Models\Competency\CompetencyType;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Route as FacadesRoute;
use App\Models\Competency\ClassCompetencyType;

class CompetencyTypeController extends Controller
{
    public function index()
    {
        if (!Auth::user()->can('competency-type-list')) {
            $response = array(
                'message' => trans('no_permission_message')
            );
            return redirect(route('home'))->withErrors($response);
        }
        
        $competencyTypes = CompetencyType::owner()->orderBy('id', 'DESC')->get();
        return response(view('competency.competency-type.index', compact('competencyTypes')));
    }

    public function store(Request $request)
    {
        if (!Auth::user()->can('competency-type-create')) {
            return response()->json([
                'error' => true,
                'message' => trans('no_permission_message')
            ]);
        }

        $validator = Validator::make($request->all(), [
            'name' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'error' => true,
                'message' => $validator->errors()->first()
            ]);
        }

        try {
            $competencyType = new CompetencyType();
            $competencyType->name = $request->name;
            $competencyType->center_id = get_center_id();
            $competencyType->save();

            return response()->json([
                'error' => false,
                'message' => trans('data_store_successfully')
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'error' => true,
                'message' => trans('error_occurred')
            ]);
        }
    }

    public function update(Request $request, $id)
    {
        if (!Auth::user()->can('competency-type-edit')) {
            return response()->json([
                'error' => true,
                'message' => trans('no_permission_message')
            ]);
        }
        
        $validator = Validator::make($request->all(), [
            'name' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'error' => true,
                'message' => $validator->errors()->first()
            ]);
        }

        try {
            $competencyType = CompetencyType::find($id);
            $competencyType->name = $request->name;
            $competencyType->save();

            return response()->json([
                'error' => false,
                'message' => trans('data_update_successfully')
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'error' => true,
                'message' => trans('error_occurred')
            ]);
        }
    }

    public function destroy($id)
    {
        if (!Auth::user()->can('competency-type-delete')) {
            return response()->json([
                'error' => true,
                'message' => trans('no_permission_message')
            ]);
        }
        
        try {
            $competencyType = CompetencyType::with('classCompetencies')->find($id);

            if ($competencyType->classCompetencies->count() > 0) {
                return response()->json([
                    'error' => true,
                    'message' => __('error_can_not_delete_competency_type_with_competencies')
                ]);
            }

            $competencyType->delete();

            return response()->json([
                'error' => false,
                'message' => trans('data_delete_successfully')
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'error' => true,
                'message' => trans('error_occurred')
            ]);
        }
    }

    
    public function assignClass(Request $request)
    {
        // if (!Auth::user()->can('competency-type-list')) {
        //     return response()->json([
        //         'error' => true,
        //         'message' => trans('no_permission_message')
        //     ]);
        // }

        $classes = ClassSchool::owner()->activeMediumOnly()->get();
        $competencyTypes = CompetencyType::owner()->where('center_id', Auth::user()->center->id)->get();
        return response(view('competency.competency-type.assgin-class', compact('classes', 'competencyTypes')));
    }

    public function assignClassStore(Request $request)
    {
        try {
            $competency_id = $request->competency_id;
            
            // Récupérer la classe
            $class = ClassSchool::find($request->class_id);
            
            // Vérifier si la compétence existe déjà pour cette classe
            $classCompetency = ClassCompetency::where('class_id', $class->id)
                ->where('competency_id', $competency_id)
                ->first();

            if (!$classCompetency) {
                // Si la compétence n'existe pas, l'ajouter
                $classCompetency = new ClassCompetency();
                $classCompetency->class_id = $class->id;
                $classCompetency->competency_id = $competency_id;
                $classCompetency->save();
            }

            // Récupérer toutes les ClassCompetencyType existantes pour cette ClassCompetency
            $existingTypes = ClassCompetencyType::where('class_competency_id', $classCompetency->id)->get();

            // Synchroniser les types de compétences
            foreach ($request->input('types', []) as $typeId => $data) {
                if (!empty($data['assigned'])) {
                    // Ajouter ou mettre à jour le type de compétence
                    ClassCompetencyType::updateOrCreate(
                        [
                            'class_competency_id' => $classCompetency->id,
                            'competency_type_id' => $typeId,
                        ],
                        [
                            'total_marks' => $data['total_marks'] ?? 0,
                        ]
                    );
                }
            }

            // Supprimer les types de compétence qui ne sont pas dans les entrées
            foreach ($existingTypes as $existingType) {
                if (!array_key_exists($existingType->competency_type_id, $request->input('types', []))) {
                    $existingType->delete();
                }
            }

            return response()->json([
                'error' => false,
                'message' => trans('data_store_successfully')
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'error' => true,
                'message' => $e->getMessage()
            ]);
        }
    }

    public function competencyList(Request $request)
    {
        $class_id = $request->class_id;
        $class = ClassSchool::owner()->with(['competencies' => function($q) use($class_id) {
            $q->where('class_id', $class_id);
        }, 'competencies.competency_types'])->find($class_id);

        $competencies = ClassCompetency::where('class_id', $class_id)->with('competencyTypes')->get();
        $competencyTypes = CompetencyType::owner()->where('center_id', Auth::user()->center->id)->get();

        return response()->json(compact('class', 'competencies', 'competencyTypes'));
    }


    public function competencyTypeAssignClass(Request $request)
    {
        $class_id = $request->class_id;
        $class = ClassSchool::find($class_id);
        $class->competency_type()->sync($request->competency_type);
        return response()->json([
            'error' => false,
            'message' => trans('data_update_successfully')
        ]);
    }

    public function show()
    {
        // if (!Auth::user()->can('competency-type-list')) {
        //     return response()->json([
        //         'error' => true,
        //         'message' => trans('no_permission_message')
        //     ]);
        // }
        // dd(request()->all());
        $offset = request('offset', 0);
        $limit = request('limit', 10);
        $sort = request('sort', 'id');
        $order = request('order', 'DESC');

        $query = CompetencyType::Owner();
        
        if ($search = request('search')) {
            $query->where(function($q) use($search) {
                $q->where('id', 'LIKE', "%$search%")
                  ->orWhere('name', 'LIKE', "%$search%");
            });
        }

        $total = $query->count();
        $rows = $query->orderBy($sort, $order)
                     ->skip($offset)
                     ->take($limit)
                     ->get()
                     ->map(function($row, $key) use($offset) {
                         return [
                             'id' => $row->id,
                             'no' => $offset + $key + 1,
                             'name' => $row->name,
                             'operate' => $this->getOperateHtml($row->id),
                             'created_at' => $row->created_at,
                             'updated_at' => $row->updated_at,
                         ];
                     });

        return response()->json([
            'total' => $total,
            'rows' => $rows
        ]);
    }

    private function getOperateHtml($id)
    {
        $editUrl = route('competency-type.edit', $id);
        $deleteUrl = route('competency-type.destroy', $id);
        
        return "<a href='{$editUrl}' class='btn btn-xs btn-gradient-primary btn-rounded btn-icon edit-data' data-id='{$id}' title='Edit' data-toggle='modal' data-target='#editModal'><i class='fa fa-edit'></i></a>&nbsp;&nbsp;" .
               "<a href='{$deleteUrl}' class='btn btn-xs btn-gradient-danger btn-rounded btn-icon delete-form' data-id='{$id}'><i class='fa fa-trash'></i></a>";
    }

} 