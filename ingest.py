import os
import sys
from dotenv import load_dotenv
load_dotenv()

from langchain.embeddings.openai import OpenAIEmbeddings
from langchain.vectorstores.pgvector import PGVector, DistanceStrategy
from langchain.document_loaders import PyPDFLoader

paths = []
if len(sys.argv) > 1:
    for i in range(1, len(sys.argv)):
        arg = sys.argv[i]
        paths.append(arg)
        break
else:
    print("No command-line arguments provided.")
    exit()

loader = PyPDFLoader(paths[0])
pages = loader.load_and_split()

embeddings = OpenAIEmbeddings()
CONNECTION_STRING = PGVector.connection_string_from_db_params(
    driver=os.environ.get("PGVECTOR_DRIVER", "psycopg2"),
    host=os.environ.get("DB_HOST", "localhost"),
    port=int(os.environ.get("DB_PORT", "5432")),
    database=os.environ.get("DB_DATABASE", "postgres"),
    user=os.environ.get("DB_USERNAME", "postgres"),
    password=os.environ.get("DB_PASSWORD", ""),
)

db = PGVector.from_documents(
    embedding=embeddings,
    documents=pages,
    collection_name=paths[0],
    connection_string=CONNECTION_STRING,
    distance_strategy=DistanceStrategy.COSINE
)

print("Finish embeddings!")
