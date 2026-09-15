import { Link, usePage } from '@inertiajs/react';
import { useEffect, useState } from 'react';

/**
 * GuestLayout – digunakan untuk halaman publik (Welcome, Login, Register)
 */
export default function GuestLayout({ children }) {
    return (
        <div className="min-h-screen bg-[#F1F5F9]">
            {children}
        </div>
    );
}
