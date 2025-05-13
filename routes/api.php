use App\Http\Controllers\Api\MovementController;
use Illuminate\Support\Facades\Route;

Route::get('/movements', [MovementController::class, 'index']);
Route::get('/movements/lead/{leadId}', [MovementController::class, 'leadMovements']);
Route::get('/movements/total', [MovementController::class, 'totalMovements']);
Route::get('/movements/unique-leads', [MovementController::class, 'uniqueLeads']);