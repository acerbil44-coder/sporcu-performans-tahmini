from sklearn.model_selection import train_test_split
from sklearn.ensemble import RandomForestRegressor
from sklearn.metrics import mean_absolute_error, mean_squared_error, r2_score
import pandas as pd

df = pd.read_csv('fitness_data.csv')

X = df.drop('Performance', axis=1)
y = df['Performance']

X_train, X_test, y_train, y_test = train_test_split(
    X, y, test_size=0.20, random_state=42
)

model = RandomForestRegressor(n_estimators=100, random_state=42)
model.fit(X_train, y_train)

pred = model.predict(X_test)

print('MAE:', mean_absolute_error(y_test, pred))
print('RMSE:', mean_squared_error(y_test, pred) ** 0.5)
print('R2:', r2_score(y_test, pred))
