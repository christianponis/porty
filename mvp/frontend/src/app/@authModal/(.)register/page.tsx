import AuthOverlay from '@/components/common/AuthOverlay';
import RegisterPage from '@/app/(auth)/register/page';

export default function RegisterModalPage() {
  return (
    <AuthOverlay>
      <RegisterPage />
    </AuthOverlay>
  );
}
