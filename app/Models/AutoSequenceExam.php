<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\AutoSequenceExam
 *
 * @property int $id
 * @property int $exam_sequence_id
 * @property int $class_section_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|AutoSequenceExam newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|AutoSequenceExam newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|AutoSequenceExam query()
 * @method static \Illuminate\Database\Eloquent\Builder|AutoSequenceExam whereClassSectionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AutoSequenceExam whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AutoSequenceExam whereExamSequenceId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AutoSequenceExam whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AutoSequenceExam whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class AutoSequenceExam extends Model
{
    use HasFactory;
}
