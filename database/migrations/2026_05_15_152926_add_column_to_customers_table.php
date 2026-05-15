<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('customers', function (Blueprint $table) {
            $table->string('type')->nullable()->default('شركة')->after('name');            // نوع العميل (فرد أو شركة)
            $table->string('CR')->nullable()->change();                   // السجل التجاري
            $table->string('vatNumber')->nullable()->change();            // الرقم الضريبي
            $table->string('national_address')->nullable()->change();     // العنوان الوطني
            $table->string('country')->nullable();                        // الدولة
            $table->string('city')->nullable();                           // المدينة
            $table->string('street')->nullable();                         // الشارع   
            $table->string('district')->nullable();                       // الحي
            $table->string('building_number')->nullable();                // رقم المبنى
            $table->string('secondary_number')->nullable();               // الرقم الفرعي
            $table->string('postal_code')->nullable();                    // الرمز البريدي
            $table->string('short_address')->nullable();                  // العنوان المختصر        
        });
    }

    public function down(): void
    {
        Schema::table('customers', function (Blueprint $table) {
            $table->dropColumn('type');
            $table->string('CR')->change();                   
            $table->string('vatNumber')->change();            
            $table->string('national_address')->change();
            $table->dropColumn('country');                       
            $table->dropColumn('city');                          
            $table->dropColumn('street');                        
            $table->dropColumn('district');                      
            $table->dropColumn('building_number');               
            $table->dropColumn('secondary_number');              
            $table->dropColumn('postal_code');                   
            $table->dropColumn('short_address');                 
        });
    }
};
