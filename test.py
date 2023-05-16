
import psycopg_pool
import openai
import os
from dotenv import load_dotenv
load_dotenv()

openai.api_key = os.getenv("OPENAI_API_KEY")
db_host="127.0.0.1"
db_user="ahmadrosid"
db_password=""
db_name="laravel_pdf_ai"
connection_string = f"host={db_host} user={db_user} dbname='{db_name}'"

pool = psycopg_pool.ConnectionPool(connection_string)

def get_embedding(text, model="text-embedding-ada-002"):
   text = text.replace("\n", " ")
   return openai.Embedding.create(input = [text], model=model)['data'][0]['embedding']

question = "What is Makefile?"

query_embedding = get_embedding(question)

with pool.connection() as conn:
    results = conn.execute("""SELECT langchain_pg_embedding.collection_id, langchain_pg_embedding.embedding, langchain_pg_embedding.document, langchain_pg_embedding.cmetadata, langchain_pg_embedding.custom_id, langchain_pg_embedding.uuid, langchain_pg_embedding.embedding <=> %s::vector AS distance 
    FROM langchain_pg_embedding JOIN langchain_pg_collection ON langchain_pg_embedding.collection_id = langchain_pg_collection.uuid 
    WHERE langchain_pg_embedding.collection_id = '534c0b78-732a-4873-ae44-f6bd72361acb'::UUID ORDER BY distance ASC 
    LIMIT 4""", [query_embedding])
    out = results.fetchall()
conn.close()

titles = [str(row[2]) for row in out]

context = ' '.join(titles);

prompt_template = """Use the following pieces of context to answer the question at the end. If you don't know the answer, just say that you don't know, don't try to make up an answer.

{context}

Question: {question}
Helpful Answer:"""

system_template = """Use the following pieces of context to answer the users question. 
If you don't know the answer, just say that you don't know, don't try to make up an answer.
----------------
{context}"""

system_prompt = system_template.replace("{context}", context)

prompt = prompt_template.replace("{context}", context).replace("{question}", question)

# result = openai.Completion.create(
#   model="text-davinci-003",
#   prompt=prompt,
#   max_tokens=1024,
#   temperature=0
# )
# print(result['choices'][0]['text'])


result = openai.ChatCompletion.create(
  model="gpt-3.5-turbo",
  temperature=0,
  messages=[
        {"role": "system", "content": system_prompt},
        {"role": "user", "content": question},
    ]
)

print(result['choices'][0]['message']['content'])
