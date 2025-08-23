import unittest
from app.models import User, SubscriptionPlan
from app.services import SubscriptionService

class TestSubscriptionSystem(unittest.TestCase):

    def setUp(self):
        self.user = User.objects.create(email='test@example.com', password='password')
        self.plan = SubscriptionPlan.objects.create(name='Test Plan', price=10)
        self.subscription_service = SubscriptionService()

    def test_assign_plan(self):
        expires_at = timezone.now() + timezone.timedelta(days=30)
        subscription = self.subscription_service.assignPlan(self.user, self.plan, expires_at)
        
        self.assertEqual(subscription.user, self.user)
        self.assertEqual(subscription.subscription_plan, self.plan)
        self.assertEqual(subscription.expires_at.date(), expires_at.date())


if __name__ == '__main__':
    unittest.main()
