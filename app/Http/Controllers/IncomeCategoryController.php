<?php

namespace App\Http\Controllers;

use App\Models\ClassSection;
use App\Models\IncomeCategory;
use App\Models\Mediums;
use App\Printing\IncomeExpensePrints;
use App\Printing\StudentPrints;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class IncomeCategoryController extends Controller
{
    public function index() {
        $data = [];

        $categories = IncomeCategory::owner()->get()->toArray();

        $data['categories'] = $categories;

        $mediums = Mediums::all()->pluck('name', 'id')->toArray();

        $mediums[0] = "All";

        return view('income-category.index', compact('data', 'mediums'));
    }

    public function store(Request $request) {

        $request->validate([
            'title' => 'required|max:191|unique:income_categories,title',
            'description' => 'required',
        ]);

        $incomeCategory = new IncomeCategory();

        $incomeCategory->title = $request->input('title');
        $incomeCategory->description = $request->input('description');
        $incomeCategory->center_id = get_center_id();
        $incomeCategory->slug = Str::slug($request->title, '-');
        $incomeCategory->medium_id = $request->input('medium', 0);

        $incomeCategory->save();

        return response()->json([
            'error' => false,
            'message' => trans("Income Category created"),
        ]);
    }

    public function show(Request $request) {

        $search = $request->input('search', '');

        $income_categories = IncomeCategory::search($search)->where('center_id', get_center_id())->get();

        $rows = $temp = [];

        $no = 1;

        foreach ($income_categories as $category) {
            $temp['no'] = $no;
            $temp['id'] = $category->id;
            $temp['title'] = $category->title;
            $temp['slug'] = $category->slug;
            $temp['medium'] = $category->medium_id == 0 ? 'All' : $category->medium->name;
            $temp['description'] = $category->description;
            $temp['created_at'] = $category->created_at;
            $rows[] = $temp;
            $no++;
        }

        if(request()->get('print')){
            $pdf = IncomeExpensePrints::getInstance(get_center_id(), 'P',add_header: false);

            $pdf->printIncomeCategories($rows);

            return response(
                $pdf->Output('', 'Income Categories.pdf'),
                200,
                [
                    'Content-Type' => 'application/pdf'
                ]
            );
        }

        $bulkData['total'] = $income_categories->count();
        $bulkData['rows'] = $rows;

        return response()->json($bulkData);
    }

    public function destroy($id) {
        try {
            DB::beginTransaction();
            $user = Auth::user();

//            if (!$user->can('delete-category')) {
//                return response()->json([
//                    'error' => true,
//                    'message' => trans("You don't have permission to perform this action"),
//                ]);
//            }

            $category = IncomeCategory::findOrFail($id);

            $category->delete();

            DB::commit();

            return response()->json([
                'error' => false,
                'message' => trans("Income Category deleted"),
            ]);

        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json([
                'error' => true,
                'message' => trans("Income Category cannot be deleted"),
            ]);
        }
    }
}
