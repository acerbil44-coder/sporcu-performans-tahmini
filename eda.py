import pandas as pd
import matplotlib.pyplot as plt
import seaborn as sns

df = pd.read_csv("fitness_data.csv")

print(df.head())
print(df.info())
print(df.describe())

print(df.isnull().sum())

plt.figure()
sns.heatmap(df.corr(), annot=True)
plt.show()

df.hist()
plt.show()
