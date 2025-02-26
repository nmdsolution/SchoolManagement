<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void {
//         making amount to float
        Schema::table('fees_classes', static function (Blueprint $table) {
            $table->float('amount')->change();
            $table->tinyInteger('choiceable')->comment('0 - no 1 - yes')->after('fees_type_id');
            $table->foreignId('center_id')->references('id')->on('centers')->onDelete('cascade');
        });

        // making adding Installment fee status , due date and due charges for the fees
        Schema::table('session_years', static function (Blueprint $table) {
            $table->tinyInteger('include_fee_installments')->comment('0 - no 1 - yes')->after('end_date')->default(0);
            $table->date('fee_due_date')->default(date('Y-m-d'))->after('include_fee_installments');
            $table->integer('fee_due_charges')->comment('in percentage (%)')->default(0)->after('fee_due_date');
        });

        // Installment Fee table
        Schema::create('installment_fees', static function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->date('due_date');
            $table->integer('due_charges')->comment('in percentage (%)');
            $table->foreignId('session_year_id')->references('id')->on('session_years')->onDelete('cascade');
            $table->foreignId('center_id')->references('id')->on('centers')->onDelete('cascade');
            $table->timestamps();
            $table->softDeletes();
        });


        Schema::create('paid_installment_fees', static function (Blueprint $table) {
            $table->id();
            $table->foreignId('class_id')->references('id')->on('classes')->onDelete('cascade');
            $table->foreignId('student_id')->references('id')->on('students')->onDelete('cascade');
            $table->foreignId('parent_id')->nullable()->references('id')->on('parents')->onDelete('cascade');
            $table->foreignId('installment_fee_id')->references('id')->on('installment_fees')->onDelete('cascade');
            $table->foreignId('session_year_id')->references('id')->on('session_years')->onDelete('cascade');
            $table->float('amount');
            $table->float('due_charges')->nullable()->default(null);
            $table->date('date');
            $table->float('amount')->change();
            $table->foreignId('payment_transaction_id')->references('id')->on('payment_transactions')->onDelete('cascade');
            $table->foreignId('center_id')->references('id')->on('centers')->onDelete('cascade');
            $table->timestamps();
        });

        // Making Payment Gateway , Order ID nullable and adding mode column
        Schema::table('payment_transactions', static function (Blueprint $table) {
            $table->smallInteger('payment_gateway')->nullable()->change();
            $table->unsignedBigInteger('parent_id')->nullable()->change();
            $table->string('order_id')->nullable()->change();
            $table->smallInteger('mode')->comment('0 - cash 1 - cheque 2 - online')->default(2)->after('parent_id');
            $table->string('cheque_no')->nullable()->after('mode');
            $table->smallInteger('type_of_fee')->comment('0 - compulsory_full , 1 - installments , 2 -optional')->default(0)->after('cheque_no');
            $table->float('total_amount')->change();
            $table->dateTime('date')->nullable()->after('total_amount');
            $table->foreignId('center_id')->references('id')->on('centers')->onDelete('cascade');
        });

        // Making mode nullable column and total amount to double
        Schema::table('fees_paids', static function (Blueprint $table) {
            $table->tinyInteger('is_fully_paid')->comment('0 - no 1 - yes')->after('total_amount')->default(1);
            $table->smallInteger('mode')->comment('0 - cash 1 - cheque 2 - online')->nullable()->change();
            $table->float('total_amount')->change();
            $table->foreignId('center_id')->references('id')->on('centers')->onDelete('cascade');
        });

        Schema::table('fees_choiceables', static function (Blueprint $table) {
            $table->float('total_amount')->change();
            $table->date('date')->after('session_year_id')->nullable()->default(null);
            $table->foreignId('payment_transaction_id')->nullable()->after('date')->references('id')->on('payment_transactions')->onDelete('cascade');
            $table->foreignId('center_id')->references('id')->on('centers')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void {
        Schema::disableForeignKeyConstraints();
        Schema::table('fees_types', static function (Blueprint $table) {
            $table->dropConstrainedForeignId('center_id');
        });
        //revert to integer amount
        Schema::table('fees_classes', static function (Blueprint $table) {
            $table->integer('amount')->change();
            $table->dropConstrainedForeignId('center_id');
        });
        //remove Installment fee status , due date and due charges for the fees columns
        Schema::table('session_years', static function (Blueprint $table) {
            $table->dropColumn('include_fee_installments');
            $table->dropColumn('fee_due_date');
            $table->dropColumn('fee_due_charges');
        });

        // remove fully paid status from fees paid
        Schema::table('fees_paids', static function (Blueprint $table) {
            $table->dropColumn('is_fully_paid');
            $table->smallInteger('mode')->comment('0 - cash 1 - cheque 2 - online')->nullable(false)->change();
            $table->integer('total_amount')->change();
            $table->dropConstrainedForeignId('center_id');
        });

        //remove the table
        Schema::dropIfExists('installment_fees');
        Schema::dropIfExists('paid_installment_fees');

        // Making Payment Gateway , Order ID nullable false and removing mode column
        Schema::table('payment_transactions', static function (Blueprint $table) {
            $table->smallInteger('payment_gateway')->nullable(false)->change();
            $table->string('order_id')->nullable(false)->change();
            $table->dropColumn('mode');
            $table->dropColumn('cheque_no');
            $table->dropColumn('type_of_fee');
            $table->integer('total_amount')->change();
            $table->dropColumn('date');
            $table->dropConstrainedForeignId('center_id');
        });


        //remove the date and changing back total amount to integer
        Schema::table('fees_choiceables', static function (Blueprint $table) {
            $table->integer('total_amount')->change();
            $table->dropColumn('date');
            $table->dropConstrainedForeignId('payment_transaction_id');
            $table->dropConstrainedForeignId('center_id');
        });
    }
};
