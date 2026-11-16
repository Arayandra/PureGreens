import pandas as pd
from mlxtend.frequent_patterns import apriori, association_rules
import json
import sys
import traceback

def load_transactions(file_path):
    try:
        df = pd.read_csv(file_path)
        if df.empty:
            return []
        transactions = df.groupby('order_id')['product_id'].apply(list).values.tolist()
        return transactions
    except Exception as e:
        return []

def encode_transactions(transactions):
    if not transactions:
        return pd.DataFrame()
    
    all_products = set()
    for transaction in transactions:
        for product in transaction:
            all_products.add(product)
    
    encoded_vals = []
    for transaction in transactions:
        labels = {product: 0 for product in all_products}
        for product in transaction:
            labels[product] = 1
        encoded_vals.append(labels)
    
    encoded_df = pd.DataFrame(encoded_vals)
    return encoded_df

def run_apriori(transactions, min_support=0.04):  # Ubah ke 0.04
    encoded_df = encode_transactions(transactions)
    if encoded_df.empty:
        return pd.DataFrame()
    
    try:
        frequent_itemsets = apriori(encoded_df, min_support=min_support, use_colnames=True)
        if frequent_itemsets.empty:
            return pd.DataFrame()
        rules = association_rules(frequent_itemsets, metric="confidence", min_threshold=0.5)
        return rules
    except Exception as e:
        return pd.DataFrame()

def get_recommendations(product_id, rules):
    if rules.empty:
        return []
    
    recommendations = rules[rules['antecedents'].apply(lambda x: product_id in x)]
    recommended_products = []
    for _, row in recommendations.iterrows():
        for product in row['consequents']:
            if product not in recommended_products:
                recommended_products.append(int(product))  # Konversi ke int
    return recommended_products

def main():
    try:
        if len(sys.argv) != 3:
            print(json.dumps({"error": "Usage: python3 script.py <csv_file> <product_id>"}))
            sys.exit(1)
        
        file_path = sys.argv[1]
        product_id = int(sys.argv[2])
        
        transactions = load_transactions(file_path)
        if not transactions:
            print(json.dumps({"error": "No valid transactions found"}))
            sys.exit(1)
        
        rules = run_apriori(transactions, min_support=0.04)
        recommendations = get_recommendations(product_id, rules)
        
        print(json.dumps(recommendations))
    except Exception as e:
        error_msg = {"error": {"message": str(e), "traceback": traceback.format_exc()}}
        print(json.dumps(error_msg))
        sys.exit(1)

if __name__ == "__main__":
    main()