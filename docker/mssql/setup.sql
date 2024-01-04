IF NOT EXISTS(SELECT * FROM sys.databases WHERE name = 'animate')
  BEGIN
    CREATE DATABASE [animate]
  END
