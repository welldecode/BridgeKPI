<?

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PlanRecurringSubscriptions extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'plan_id',
        'next_billing_date', // Próxima data de cobrança
        'stats', // Exemplo: 'active', 'pending', 'canceled'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}