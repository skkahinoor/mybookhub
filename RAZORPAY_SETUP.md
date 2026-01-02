# Razorpay Payment Integration Setup

## Environment Configuration

Add the following environment variables to your `.env` file:

```env
# Razorpay Configuration
RAZORPAY_KEY_ID=your_razorpay_key_id_here
RAZORPAY_KEY_SECRET=your_razorpay_key_secret_here
```

## Getting Razorpay Credentials

1. **Sign up/Login to Razorpay Dashboard**
   - Visit: https://dashboard.razorpay.com/
   - Create an account or login

2. **Get API Keys**
   - Go to: Settings → API Keys
   - Click "Generate Key" if you don't have keys
   - Copy the **Key ID** and **Key Secret**

3. **Test Mode vs Live Mode**
   - **Test Mode**: Use test keys for development (starts with `rzp_test_`)
   - **Live Mode**: Use live keys for production (starts with `rzp_live_`)

## Testing Payment Flow

### Test Mode Setup

1. Add test credentials to `.env`:
```env
RAZORPAY_KEY_ID=rzp_test_xxxxxxxxxxxxx
RAZORPAY_KEY_SECRET=xxxxxxxxxxxxxxxxxxxxx
```

2. **Test Cards** (for test mode):
   - **Success**: `4111 1111 1111 1111`
   - **Failure**: `4000 0000 0000 0002`
   - **CVV**: Any 3 digits
   - **Expiry**: Any future date
   - **Name**: Any name

3. **Test Payment Flow**:
   - Register a vendor and select "Pro Plan"
   - You'll be redirected to Razorpay payment page
   - Use test card: `4111 1111 1111 1111`
   - Complete payment
   - Verify payment success

### Testing Commands

Run the subscription renewal command manually:
```bash
php artisan vendor:renew-subscriptions
```

This will:
- Check for expired Pro plans
- Downgrade expired plans to Free
- Log subscription status

## Production Setup

1. **Switch to Live Mode**:
   - Get live API keys from Razorpay dashboard
   - Update `.env` with live credentials
   - Ensure webhook URLs are configured (if using webhooks)

2. **Webhook Configuration** (Optional):
   - Go to: Settings → Webhooks
   - Add webhook URL: `https://yourdomain.com/api/razorpay/webhook`
   - Select events: `payment.captured`, `payment.failed`

## Subscription Renewal

The system automatically checks and renews subscriptions daily via scheduled task:

```php
// Runs daily at midnight
$schedule->command('vendor:renew-subscriptions')->daily()->at('00:00');
```

To run manually:
```bash
php artisan vendor:renew-subscriptions
```

## Plan Features

### Free Plan
- Up to 100 books per month
- No coupon creation
- Basic vendor dashboard

### Pro Plan (₹499/month)
- Unlimited book uploads
- Unlimited coupons
- Priority support
- 1 month subscription period

## Troubleshooting

### Payment Not Processing
1. Check Razorpay credentials in `.env`
2. Verify API keys are correct (test vs live)
3. Check server logs: `storage/logs/laravel.log`
4. Ensure Razorpay checkout script is loading

### Subscription Not Renewing
1. Check cron job is running: `php artisan schedule:run`
2. Verify command exists: `php artisan list | grep vendor`
3. Check logs for errors

### Middleware Issues
1. Ensure middleware is registered in `app/Http/Kernel.php`
2. Check routes have `vendor.plan` middleware applied
3. Verify vendor authentication is working

## Support

For Razorpay API issues:
- Documentation: https://razorpay.com/docs/
- Support: support@razorpay.com

For application issues:
- Check Laravel logs: `storage/logs/laravel.log`
- Review middleware: `app/Http/Middleware/CheckVendorPlan.php`

