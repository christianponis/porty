import AuthOverlay from '@/components/common/AuthOverlay';
import LoginPage from '@/app/(auth)/login/page';

export default function LoginModalPage() {
  return (
    <AuthOverlay>
      <LoginPage />
    </AuthOverlay>
  );
}
