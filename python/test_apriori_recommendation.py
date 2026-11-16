import unittest

import pandas as pd

from apriori_recommendation import get_recommendations, load_transactions


class AprioriRecommendationTests(unittest.TestCase):
    def test_only_single_item_antecedents_are_used_for_single_product_input(self):
        rules = pd.DataFrame(
            [
                {
                    "antecedents": frozenset({1}),
                    "consequents": frozenset({2}),
                    "confidence": 0.8,
                    "lift": 1.5,
                    "support": 0.2,
                },
                {
                    "antecedents": frozenset({1, 3}),
                    "consequents": frozenset({4}),
                    "confidence": 0.99,
                    "lift": 2.0,
                    "support": 0.1,
                },
            ]
        )

        self.assertEqual(get_recommendations(1, rules), [2])

    def test_recommendations_are_ranked_and_deduplicated(self):
        rules = pd.DataFrame(
            [
                {
                    "antecedents": frozenset({1}),
                    "consequents": frozenset({2, 3}),
                    "confidence": 0.7,
                    "lift": 1.4,
                    "support": 0.1,
                },
                {
                    "antecedents": frozenset({1}),
                    "consequents": frozenset({3, 4}),
                    "confidence": 0.9,
                    "lift": 1.8,
                    "support": 0.15,
                },
                {
                    "antecedents": frozenset({1}),
                    "consequents": frozenset({1}),
                    "confidence": 0.95,
                    "lift": 2.0,
                    "support": 0.2,
                },
            ]
        )

        self.assertEqual(get_recommendations(1, rules), [3, 4, 2])

    def test_empty_rules_return_an_empty_recommendation_list(self):
        self.assertEqual(get_recommendations(1, pd.DataFrame()), [])

    def test_load_transactions_groups_unique_products_by_order(self):
        import tempfile
        from pathlib import Path

        with tempfile.TemporaryDirectory() as directory:
            csv_path = Path(directory) / "transactions.csv"
            pd.DataFrame(
                [
                    {"order_id": 10, "product_id": 1},
                    {"order_id": 10, "product_id": 1},
                    {"order_id": 10, "product_id": 2},
                    {"order_id": 11, "product_id": 3},
                ]
            ).to_csv(csv_path, index=False)

            self.assertEqual(load_transactions(str(csv_path)), [[1, 2], [3]])


if __name__ == "__main__":
    unittest.main()
