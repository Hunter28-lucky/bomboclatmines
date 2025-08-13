"use client"

import { useState } from 'react'
// import { supabase } from '@/lib/supabase' // Not available in bomboclatmines
// import { Button } from './ui/button' // Not available in bomboclatmines
// import { Card, CardContent, CardHeader, CardTitle, CardDescription } from './ui/card' // Not available in bomboclatmines
// import { Input } from './ui/input' // Not available in bomboclatmines
// import { Label } from './ui/label' // Not available in bomboclatmines
import { Upload, CheckCircle, CreditCard, Loader2 } from 'lucide-react'

interface PaymentWidgetProps {
  siteId: string
}

// Utility to get API endpoint from env or default
const API_URL = import.meta.env.VITE_PAYMENT_API_URL || '/api/submit-payment';

export default function PaymentWidget({ siteId }: PaymentWidgetProps) {
  const [utrNumber, setUtrNumber] = useState('')
  const [emailPhone, setEmailPhone] = useState('')
  const [screenshot, setScreenshot] = useState<File | null>(null)
  const [screenshotPreview, setScreenshotPreview] = useState<string | null>(null)
  const [isSubmitting, setIsSubmitting] = useState(false)
  const [isSuccess, setIsSuccess] = useState(false)
  const [error, setError] = useState('')

  const handleScreenshotChange = (e: React.ChangeEvent<HTMLInputElement>) => {
    const file = e.target.files?.[0]
    if (file) {
      setScreenshot(file)
      const reader = new FileReader()
      reader.onload = (e) => {
        setScreenshotPreview(e.target?.result as string)
      }
      reader.readAsDataURL(file)
    }
  }

  const handleSubmit = async (e: React.FormEvent) => {
    e.preventDefault();
    setIsSubmitting(true);
    setError('');
    try {
      let screenshotUrl = '';
      // Upload screenshot if present (optional, only if you have a public upload endpoint)
      if (screenshot) {
        // TODO: Implement screenshot upload if needed, or skip for now
      }
      // Post payment data to central API
      const response = await fetch(API_URL, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({
          utr_number: utrNumber,
          mobile_number: emailPhone,
          screenshot_url: screenshotUrl,
          site_id: siteId,
        }),
      });
      if (!response.ok) {
        const errorData = await response.json();
        throw new Error(errorData.error || 'Failed to submit payment');
      }
      setIsSuccess(true);
      setUtrNumber('');
      setEmailPhone('');
      setScreenshot(null);
      setScreenshotPreview(null);
    } catch (err: any) {
      setError(err.message || 'An error occurred');
    } finally {
      setIsSubmitting(false);
    }
  };

  // Animated checkmark SVG for success
  const AnimatedCheck = () => (
    <svg className="mx-auto mb-4" width="64" height="64" viewBox="0 0 64 64">
      <circle cx="32" cy="32" r="30" fill="#d1fae5" />
      <path
        d="M20 34l8 8 16-16"
        fill="none"
        stroke="#10b981"
        strokeWidth="4"
        strokeLinecap="round"
        strokeLinejoin="round"
        style={{ strokeDasharray: 48, strokeDashoffset: 0, animation: 'dash 0.7s ease forwards' }}
      />
      <style>{`@keyframes dash { from { stroke-dashoffset: 48; } to { stroke-dashoffset: 0; } }`}</style>
    </svg>
  )

  if (isSuccess) {
    return (
      <div className="w-full max-w-md mx-auto px-2 sm:px-0">
        <div className="bg-white/80 backdrop-blur-lg rounded-3xl shadow-2xl p-6 sm:p-8 flex flex-col items-center animate-fade-in">
          <AnimatedCheck />
          <h2 className="text-2xl font-bold text-gray-900 mb-2 text-center">Payment Submitted!</h2>
          <p className="text-gray-600 mb-6 text-center">
            Your payment has been successfully submitted and is pending verification.
          </p>
          <div className="text-xs text-gray-400 mt-4">Powered by <span className="font-bold text-blue-600">RazorPay™</span></div>
        </div>
      </div>
    )
  }

  return (
    <div className="w-full max-w-md mx-auto px-2 sm:px-0" style={{ maxWidth: '100%', width: '100%', maxHeight: '100vh', overflowY: 'auto', boxSizing: 'border-box' }}>
      <div className="bg-white/80 backdrop-blur-lg rounded-3xl shadow-2xl p-0 animate-fade-in" style={{ maxWidth: '100%', width: '100%' }}>
        {/* Brand Header */}
        <div className="flex flex-col items-center justify-center pt-8 pb-4 bg-gradient-to-br from-blue-100 to-blue-50 rounded-t-3xl">
          <div className="w-14 h-14 bg-blue-200 rounded-full flex items-center justify-center mb-2 shadow-lg">
            <CreditCard className="h-8 w-8 text-blue-600" />
          </div>
          <h1 className="text-xl font-bold text-blue-700 tracking-tight">RazorPay™</h1>
          <span className="text-xs text-blue-400 font-medium">Secure UPI Payment</span>
        </div>
        {/* QR Code Section */}
        <div className="flex flex-col items-center justify-center pt-6 pb-2">
          <div className="bg-white p-2 sm:p-3 rounded-2xl shadow-md border border-blue-100">
            <img
              src="/qrpayment.jpeg"
              alt="UPI QR Code"
              className="w-32 h-32 sm:w-40 sm:h-40 object-contain rounded-xl select-none"
              draggable={false}
              style={{ maxWidth: '100%', height: 'auto' }}
            />
          </div>
          <p className="text-xs text-gray-500 mt-2 mb-1 text-center">Scan this QR code with any UPI app to pay</p>
        </div>
        {/* Step Indicator */}
        <div className="flex items-center justify-center gap-2 py-2">
          <div className="w-2 h-2 rounded-full bg-blue-600 animate-pulse" />
          <span className="text-xs text-gray-500">Step 1 of 1: Enter Payment Details</span>
        </div>
        <form onSubmit={handleSubmit} className="p-4 sm:p-8 space-y-6 relative" style={{ paddingBottom: '5.5rem' }}>
          <div className="space-y-2">
            {/* Replace Label and Input with native elements for bomboclatmines */}
            <label htmlFor="utr" className="text-sm font-semibold text-gray-700">
              UTR Number <span className="text-red-500">*</span>
            </label>
            <input
              id="utr"
              type="number"
              inputMode="numeric"
              pattern="[0-9]*"
              autoComplete="off"
              placeholder="Enter your UTR number"
              value={utrNumber}
              onChange={(e) => setUtrNumber(e.target.value.replace(/[^0-9]/g, ''))}
              required
              className="h-14 rounded-2xl text-base px-4 border-blue-200 focus:ring-2 focus:ring-blue-400 transition-all duration-150 text-gray-900 placeholder-gray-500 bg-white"
            />
            <p className="text-xs text-gray-400">
              Unique Transaction Reference number from your payment
            </p>
          </div>
          <div className="space-y-2">
            <label htmlFor="contact" className="text-sm font-semibold text-gray-700">
              Mobile Number
            </label>
            <input
              id="contact"
              type="tel"
              inputMode="tel"
              pattern="[0-9]{10}"
              autoComplete="tel"
              placeholder="+91XXXXXXXXXX"
              value={emailPhone}
              onChange={(e) => setEmailPhone(e.target.value.replace(/[^0-9+]/g, ''))}
              className="h-14 rounded-2xl text-base px-4 border-blue-200 focus:ring-2 focus:ring-blue-400 transition-all duration-150 text-gray-900 placeholder-gray-500 bg-white"
            />
            <p className="text-xs text-gray-400">
              For payment verification updates (optional)
            </p>
          </div>
          <div className="space-y-2">
            <label htmlFor="screenshot" className="text-sm font-semibold text-gray-700">
              Payment Screenshot
            </label>
            <div className="border-2 border-dashed border-blue-200 rounded-2xl p-4 sm:p-6 text-center hover:border-blue-400 transition-colors bg-blue-50/30 active:scale-[0.98] select-none">
              <input
                id="screenshot"
                type="file"
                accept="image/*"
                onChange={handleScreenshotChange}
                className="hidden"
              />
              <label htmlFor="screenshot" className="cursor-pointer block">
                {screenshotPreview ? (
                  <div className="space-y-2">
                    <img
                      src={screenshotPreview}
                      alt="Screenshot preview"
                      className="mx-auto max-h-32 rounded-2xl border"
                      draggable={false}
                      style={{ maxWidth: '100%', height: 'auto' }}
                    />
                    <p className="text-sm text-gray-600 truncate">
                      {screenshot?.name}
                    </p>
                  </div>
                ) : (
                  <div className="space-y-2">
                    <Upload className="mx-auto h-8 w-8 text-blue-400" />
                    <p className="text-sm text-gray-600">
                      Tap to upload payment screenshot
                    </p>
                    <p className="text-xs text-gray-400">
                      PNG, JPG, JPEG up to 5MB
                    </p>
                  </div>
                )}
              </label>
            </div>
          </div>
          {error && (
            <div className="text-red-600 text-sm bg-red-50 p-3 rounded-2xl border border-red-200 animate-shake">
              {error}
            </div>
          )}
          {/* Sticky submit button for mobile and all containers */}
          <div className="sticky bottom-0 left-0 right-0 z-10 bg-white/90 px-2 py-3 rounded-b-3xl shadow-t flex justify-center" style={{ width: '100%', maxWidth: '100%' }}>
            <button
              type="submit"
              className="w-full h-14 rounded-2xl bg-gradient-to-r from-blue-600 to-blue-500 hover:from-blue-700 hover:to-blue-600 text-lg font-semibold shadow-lg transition-all duration-200 active:scale-[0.98]"
              disabled={isSubmitting}
            >
              {isSubmitting ? (
                <span className="flex items-center justify-center gap-2">
                  <Loader2 className="h-5 w-5 animate-spin" />
                  Submitting...
                </span>
              ) : (
                'Submit Payment'
              )}
            </button>
          </div>
          <div className="text-xs text-gray-400 text-center pt-4 pb-2 sm:pb-0">Powered by <span className="font-bold text-blue-600">KrishPay™</span></div>
        </form>
      </div>
      <style>{`
        .animate-fade-in { animation: fadeIn 0.7s cubic-bezier(.4,0,.2,1) both; }
        @keyframes fadeIn { from { opacity: 0; transform: translateY(24px);} to { opacity: 1; transform: none; } }
        .animate-shake { animation: shake 0.3s linear; }
        @keyframes shake { 10%, 90% { transform: translateX(-2px); } 20%, 80% { transform: translateX(4px); } 30%, 50%, 70% { transform: translateX(-8px); } 40%, 60% { transform: translateX(8px); } }
      `}</style>
    </div>
  )
} 